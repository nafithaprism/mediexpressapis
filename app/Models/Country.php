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

    
    public function getWeightBasedShippingAttribute(): array
    {
        $value = $this->attributes['weight_based_shipping'] ?? null;
        
        if (!$value || $value === 'null') {
            return [];
        }
        
        $decoded = json_decode($value, true);
        return $decoded ?? [];
    }
}