<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'value', 'valid_till', 'active', 'influencer', 'usage_count', 'is_eligible_for_commission',   
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'valid_till' => 'datetime',
        'active' => 'boolean',   'usage_count' => 'integer',                  
        'is_eligible_for_commission' => 'boolean',    
    ];

    // Always store code uppercase without spaces
    public function setCodeAttribute($v): void
    {
        $this->attributes['code'] = strtoupper(trim($v));
    }

    public function influencer()
    {
        return $this->belongsTo(User::class, 'influencer_id');
    }
}
