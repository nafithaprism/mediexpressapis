<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriceVariation extends Model
{
    use HasFactory;
    protected $table = 'product_price_variations';
    protected $fillable = ['product_id', 'country_id', 'pack_of','weight', 'actual_price', 'deal_price', 'sale_price'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
