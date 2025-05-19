<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempUser extends Model
{
    use HasFactory;
    protected $table = 'temp_users';
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'token',
        'redirect_url',
        'isActive',
        'code',
        'mobile',
        'last_name'

    ];
}
