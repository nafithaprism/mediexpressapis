<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductChildCategory extends Model
{
    use HasFactory;
    protected $table = 'product_child_categories_pivot';

    protected $fillable = ['product_id', 'child_category_id'];
}
