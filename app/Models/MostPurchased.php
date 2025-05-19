<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MostPurchased extends Model
{
    use HasFactory;
    protected $table = 'most_purchaseds';

    protected $fillable = ['product_id', 'country_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
