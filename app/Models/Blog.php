<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $table = 'blogs';

    protected $fillable = ['title', 'tags', 'route', 'featured_img', 'slider_img', 'description', 'short_description'];
    protected $casts = [
        'slider_img' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'route';
    }

    public function comments()
    {
        return  $this->hasMany(Comment::class, 'blog_id', 'id')->select('id', 'blog_id', 'first_name', 'comment', 'status', 'created_at');
    }
}
