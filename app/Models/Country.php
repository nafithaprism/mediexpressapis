<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';
     protected $fillable = [
        'name',            // mapped to countryName in API via accessors
        'route',
        'currency',
        'weight_based_shipping',
    ];

 protected $casts = [
        'weight_based_shipping' => 'array', // JSON <-> array
    ];

protected $appends = [
        'countryName',
        'weightBasedShipping',
    ];

 public function getCountryNameAttribute(): ?string
    {
        return $this->name;
    }

public function getWeightBasedShippingAttribute(): ?array
    {
        return $this->weight_based_shipping;
    }

public function setCountryNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
    }

public function setWeightBasedShippingAttribute($value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['weight_based_shipping'] = json_encode($decoded ?? []);
        } else {
            $this->attributes['weight_based_shipping'] = json_encode($value ?? []);
        }
    }












}
