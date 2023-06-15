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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        $xmlFile = $request->file('upload')->getRealPath();

        // error message bag
        $msg = [];

        try {
            // try to parse xml
            if ($reader->open('file://' . $xmlFile) === false) {
                throw new XmlParseException("Failed loading XML\n");
            }

            $reader->setParserProperty(XMLReader::VALIDATE, true);

            // set schema
            $reader->setSchema(public_path('xsd/unas.xsd'));

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
                        //dd($xlmObject);

                        $model = ProductService::makeFromXml($xlmObject);
                        $collection->add($model);

                        //$upsert = Product::upsert([$model->toArray()], 'number');



                        //dd('dd', $model->toArray());
                        $reader->next();
                    }
                }
            }

            // handling errors
            if (!empty($msgs)) {
                throw new XmlParseException("XML schema validation errors:\n - " . implode("\n - ", array_unique($msgs)));
            }


            // Upsert data into database
            $collection
                ->chunk(1000)
                ->each(fn(Collection $chunk) => Product::upsert($chunk->toArray(), 'number'));

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

        return redirect()->route('xml-parser-v2.index')->with('flash', "Items imported successfully! <br> {$benchmark['count']} items in {$benchmark['exec_time']} sec.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


}
