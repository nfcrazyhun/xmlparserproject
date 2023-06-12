<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const DEFAULT_IMAGE_URL= "https://www.onlinepenztarca.hu/images/product/no-termek-image.webp";

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'category' => 'array',
        'custom_fields' => 'array',
        'status' => 'boolean'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($product) {
            // If data are not provided on creation, set property to empty json array.
            if ($product->category === null) {
                $product->category = [];
            }

            if ($product->custom_fields === null) {
                $product->custom_fields = [];
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Accessors & Mutators
    |--------------------------------------------------------------------------
    */
    /**
     * Get the user's first name.
     */
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => !empty($value) ? $value : $this->DEFAULT_IMAGE_URL,
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
}
