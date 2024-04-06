<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'imdb_id',
        'title',
        'plot',
        'poster',
        'year',
        'rating',
        'length',
        'released',
        'genre',
        'director',
        'writer',
        'actor',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class, 'imdb_id', 'imdb_id');
    }
}
