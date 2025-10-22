<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    
    protected $table = 'countries';
    
    protected $fillable = [
        'name',
        'route',
        'currency',
        'weight_based_shipping',
    ];

    protected $casts = [
        'weight_based_shipping' => 'array',
    ];

    protected $appends = [
        'weightBasedShipping',
    ];

    public function getWeightBasedShippingAttribute(): ?array
    {
        return json_decode($this->attributes['weight_based_shipping'] ?? null, true);
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
