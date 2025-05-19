<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';
    protected $fillable = ['name', 'route', 'standard_shipping_charges', 'express_shipping_charges' ,'currency'];

    // public function country()
    // {
    //     return $this->hasMany(Country::class);
    // }
}
