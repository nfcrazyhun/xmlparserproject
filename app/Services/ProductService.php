<?php

namespace App\Services;

use App\Exceptions\XmlParseException;
use App\Models\Product;
use SimpleXMLElement;
use XMLReader;

class ProductService
{
    /**
     * Update an existing product or Create a new one from XLM object.
     */
    public static function updateOrCreateFromXml(SimpleXMLElement $product): Product
    {
        $productCategories = [];
        foreach ($product->category->children() as $category) {
            $productCategories[] = (string) $category;
        }

        $productCustomFields = [];
        foreach ($product->customFields->children() as $cf) {
            $key = (string) $cf->attributes()->name;
            $value = (string) $cf;
            $productCustomFields[$key] = $value;
        }

        // todo: refactor into upsert: https://www.youtube.com/watch?v=J8x268as2qo | https://www.youtube.com/watch?v=1LN2b599xN8
        return Product::updateOrCreate(
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
    }

    /**
     * Make a Product object in memory from XLM object.
     */
    public static function makeFromXml(SimpleXMLElement $product): Product
    {
        $productCategories = [];
        foreach ($product->category->children() as $category) {
            $productCategories[] = (string) $category;
        }

        $productCustomFields = [];
        foreach ($product->customFields->children() as $cf) {
            $key = (string) $cf->attributes()->name;
            $value = (string) $cf;
            $productCustomFields[$key] = $value;
        }

        return Product::make([
            'number' => (string) $product->number,
            'name' => (string) $product->name,
            'category' => json_encode($productCategories),
            'price' => (int) $product->price,
            'url' => (string) $product->url,
            'image' => (string) $product->image,
            'description' => (string) $product->description,
            'stock' => (int) $product->stock,
            'status' => (bool)(int) $product->status,
            'custom_fields' => json_encode($productCustomFields),
        ]);
    }

    /**
     * Guess the name of the XML Schema.
     * Returns the path for the correct schema.
     */
    public static function guessSchema($xmlPath): string
    {
        // List of schemas
        $schemas = [
            'productFeed' => storage_path('xsd/unas.xsd'),
            'Product' => storage_path('xsd/arukereso.xsd'),
        ];

        $reader = new XMLReader();

        // try to parse xml
        if ($reader->open('file://' . $xmlPath) === false) {
            throw new XmlParseException("Failed loading XML\n");
        }

        // read the root's node name
        $reader->read();
        $rootName = $reader->name;
        $reader->close();

        return $schemas[$rootName];
    }
}
