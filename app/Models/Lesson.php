<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id', 'title', 'description',
        'video_url_male', 'video_url_female',
        'attachment_url', 'order_number'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
