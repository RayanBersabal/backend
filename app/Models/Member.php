<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'task',
        'image',
        'github',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role' => 'array', // Casting ke array saat diambil dari DB
        'task' => 'array', // Casting ke array saat diambil dari DB
    ];
}
