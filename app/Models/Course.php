<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title', 'description', 'price', 'thumbnail'];

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
