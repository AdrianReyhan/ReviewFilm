<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'movie_id',
        'title',
        'poster',
        'year',
        'user_id',
        'user_name',
        'rating',
        'comment'
    ];

    // Define the relationship with the Movie model
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
