<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'country',
        'city',
        'state',
        'street_address',
        'address_type',
        'status',
        'postal_code',
        'default',
        'address_line1',
        'address_line2',
        'mobile',
    ];
}