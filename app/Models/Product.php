<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';

    protected $fillable = ['name', 'route', 'sku', 'product_type', 'stock', 'featured_img', 'short_description', 'description', 'additional_information', 'slider_img', 'tags', 'brand_id', 'stock'];
    protected $casts = [
        'slider_img' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'route';
    }

    public function price()
    {
        return $this->hasMany(ProductPriceVariation::class);
    }


    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {

        return $this->belongsToMany(Category::class, 'product_categories_pivot');
    }

    public function subCategory()
    {
        return $this->belongsToMany(SubCategory::class, 'product_sub_categories_pivot');
    }

    public function childCategory()
    {
        return $this->belongsToMany(ChildCategory::class, 'product_child_categories_pivot');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }

    public function available_videos()
    {
        return $this->hasMany(ProductPriceVariation::class)->where('country_id', '=', 3);
    }

    public function mostPurchasedCountry()
    {
        return $this->hasManyThrough(
            MostPurchased::class,
            Country::class,
        );
    }
}
