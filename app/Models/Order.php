<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_number',
        'tracking_number',
        'logistics_partner_name',
        'logistics_partner_link',
        'user_id',
        'billing_address_id',
        'payment_type',
        'sub_total',
        'discounted_amount',
        'total_amount',
        'currency',
        'country_id',
        'shipping_charges',
        'payment_status',
        'status'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }


    public function billingAddress()
    {

        return $this->belongsTo(Address::class);
    }
}