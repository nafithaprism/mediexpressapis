<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $table = 'pages';

    protected $fillable = ['name', 'route', 'identifier', 'content'];
    protected $casts = [
        'content' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'route';
    }
}
