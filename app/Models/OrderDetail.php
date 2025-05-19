<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'country_id',
        'qty',
        'product_id',
        'product_price_variation'

    ];

    public function product()
    {

        return $this->hasMany(Product::class, 'id', 'product_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }

    public function order()
    {

        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function country()
    {

        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}