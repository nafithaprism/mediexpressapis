<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories_pivot';

    protected $fillable = ['product_id', 'category_id'];

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
