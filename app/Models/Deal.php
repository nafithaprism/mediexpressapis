<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $table = 'deals';

    protected $fillable = ['deal_type', 'product_id',  'country_id' , 'variation_id','status'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
