<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Movie extends Model
{
     use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'cdn_path',
        'rental_expires_at',
    ];

    protected $casts = [
        'rental_expires_at' => 'datetime',
    ];
}
