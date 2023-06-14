<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreXmlParserRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class XmlParserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('xml-parser.index');
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
     *
     * @example
     * <?xml version="1.0" encoding="UTF-8" ?>
     * <productFeed>
     *     <product>
     *         <id><![CDATA[104132626]]></id>
     *         <number><![CDATA[993504]]></number>
     *         <name><![CDATA[Bottari Stylish kormányvédő]]></name>
     *         <category>
     *             <category1><![CDATA[Univerzális autófelszerelés]]></category1>
     *             <category2><![CDATA[Férfias és unisex kiegészítők]]></category2>
     *             <category3><![CDATA[Kormányvédő]]></category3>
     *         </category>
     *         <price><![CDATA[1985]]></price>
     *         <url><![CDATA[/Bottari-Stylish]]></url>
     *         <image><![CDATA[/shop_ordered/33569/shop_pic/993504.jpg]]></image>
     *         <description><![CDATA[Bottari Stylish kormányvédő fekete 38cm átmérőjű kormányokhoz- (személyautó kormány) Tökéletes választás, kopottabb kormányok felújítására és a kormány védelmére. ]]></description>
     *         <stock><![CDATA[off]]></stock>
     *         <status><![CDATA[1]]></status>
     *         <customFields>
     *             <property name="Önhöz várhatóan ekkor érkezik meg a termék-"><![CDATA[3]]></property>
     *             <property name="Szállítási Idő"><![CDATA[2]]></property>
     *         </customFields>
     *     </product>
     *     <product>...</product>
     * <productFeed>
     */
    public function store(StoreXmlParserRequest $request)
    {
        libxml_use_internal_errors(true);

        try {
            // parse xml
            $xml = simplexml_load_file($request->file('upload')->getRealPath());

            // check if xml parsed successfully
            if ($xml === false) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            // show errors on screen
            echo '<pre>';
            echo "Failed loading XML\n";

            foreach(libxml_get_errors() as $error) {
                echo "\t", $error->message;
            }
            echo '</pre>';

            exit();
        }

        //dump($xml->count());    // 182
        //dump($xml->getName());  // "productFeed"
        //dump($xml->children()->getName());  // "product"
        //dump($xml);  // (array)
        //echo '<pre>'; print_r($xml); echo '</pre>';   // (array)

        //dump($xml->children()[0]->asXML());

        $i = 0;
        foreach ($xml->children() as $product) {

            // [LÁNCOK, KARLÁNCOK, Sárga arany]
            $productCategories = [];
            foreach ($product->category->children() as $category) {
                $productCategories[] = (string) $category;
            }

            $productCustomFields = [];
            foreach ($product->customFields->children() as $cf) {
                $key = (string) $cf->attributes()->name;
                //$value = explode(',', (string) $cf);
                //$value = array_map('trim', $value);
                $value = (string) $cf;
                $productCustomFields[$key] = $value;
            }


            // todo: refactor into upsert: https://www.youtube.com/watch?v=J8x268as2qo | https://www.youtube.com/watch?v=1LN2b599xN8
            Product::updateOrCreate(
                ['number' => (string) $product->number],    // where
                [
                    'name' => (string) $product->name,
                    'category' => $productCategories,
                    'price' => (int) $product->price,
                    'url' => (string) $product->url,
                    'image' => (string) $product->image,
                    'description' => (string) $product->description,
                    'stock' => (int) $product->stock,
                    'status' => (bool)(int) $product->status,
                    'custom_fields' => $productCustomFields
                ]
            );

            $i++;
            if ($i > 10) { break;}
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
