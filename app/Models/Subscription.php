<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'start_date', 'end_date', 'status'
    ];

    public function course()
{
    return $this->belongsTo(\App\Models\Course::class);
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
