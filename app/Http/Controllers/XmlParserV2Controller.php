<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreXmlParserRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use XMLReader;
use SimpleXMLElement;

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
        // xml reader instance
        $reader = new XMLReader();

        // xml file to read
        $xmlFile = $request->file('upload')->getRealPath();

        try {
            // try to parse xml
            if( $reader->open('file://'.$xmlFile) === false) {
                throw new \Exception("Failed loading XML\n");
            }

            while ($reader->read()) {
                if ($reader->nodeType == XMLReader::ELEMENT){
                    if ($reader->name == 'product'){
                        $outerXml = $reader->readouterXml();
                        $xlmObject = simplexml_load_string($outerXml);

                        $model = ProductService::updateOrCreateFromXml($xlmObject);

                        //dd('dd', $model);
                    }
                }
            }
        }
        catch (\Exception $e) {
            // show errors on screen
            echo $e->getMessage();
        }
        finally {
            $reader->close();
        }

        return redirect()->route('xml-parser.index')->with('flash','Item created successfully!');
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
