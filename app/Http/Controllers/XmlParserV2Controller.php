<?php

namespace App\Http\Controllers;

use App\Exceptions\XmlParseException;
use App\Http\Requests\StoreXmlParserRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use XMLReader;

class XmlParserV2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('xml-parser-v2.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreXmlParserRequest $request)
    {
        // Benchmark start
        $start_time = microtime(true);

        libxml_use_internal_errors(true);

        // xml reader instance
        $reader = new XMLReader();

        // xml file to read
        $xmlPath = $request->file('upload')->getRealPath();

        // error message bag
        $msg = [];

        // logs
        $logChannel = null;
        $logPath = storage_path('logs/'.(Str::slug(now()->toDateTimeString()).'.log'));

        try {
            // try to parse xml
            if ($reader->open('file://' . $xmlPath) === false) {
                throw new XmlParseException("Failed loading XML\n");
            }

            // enable validate
            $reader->setParserProperty(XMLReader::VALIDATE, true);

            // guess schema
            $schemaPath = ProductService::guessSchema($xmlPath);

            // set schema
            $reader->setSchema($schemaPath);

            $collection = new Collection();

            while ($reader->read()) {
                // Validate xml
                if ($reader->isValid() === false) {
                    // log errors
                    $err = \libxml_get_last_error();
                    if ($err && $err instanceof \libXMLError) {
                        $msgs[] = \trim($err->message) . ' on line ' . $err->line;
                    }
                }

                // process xml
                if ($reader->nodeType == XMLReader::ELEMENT) {
                    if ($reader->name == 'product') {
                        $outerXml = $reader->readouterXml();
                        $xlmObject = simplexml_load_string($outerXml);

                        $model = ProductService::makeFromXml($xlmObject);
                        $collection->add($model);

                        $reader->next();
                    }
                }
            }


            // handling errors
            if (!empty($msgs)) {
                // disabled temporary
                //throw new XmlParseException("XML schema validation errors:\n - " . implode("\n - ", array_unique($msgs)));

                // log errors in file
                $logChannel = Log::build([
                    'driver' => 'single',
                    'path' => $logPath,
                ]);
                $logChannel->info( "XML schema validation errors:\n - " . implode("\n - ", array_unique($msgs)) );
            }


            // Upsert data into database
            DB::transaction(function () use ($collection){
                $collection
                    ->chunk(1000)
                    ->each(fn(Collection $chunk) => Product::upsert($chunk->toArray(), 'number'));
            });


        } catch (XmlParseException $e) {
            // show errors on screen
            echo '<pre>';
            echo $e->getMessage();
            echo '</pre>';

            $reader->close();
            die();
        }
//        catch (\Exception $e) {   // Note: The default exception has been intentionally omitted to let Laravel catch the errors for easier debugging.
//            // show errors on screen
//            echo $e->getMessage();
//            $reader->close();
//            die();
//        }
        finally {
            $reader->close();
        }

        // Benchmark end
        $end_time = microtime(true);
        $benchmark = [
            'count' => $collection->count(),
            'exec_time' => number_format($end_time - $start_time, 3),
        ];

        return redirect()->route('xml-parser-v2.index')
            ->with('flash', "Items imported successfully! <br> {$benchmark['count']} items in {$benchmark['exec_time']} sec.")
            ;
    }
}
