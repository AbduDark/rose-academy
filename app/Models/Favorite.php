<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    //
    public function course()
{
    return $this->belongsTo(\App\Models\Course::class);
}
}
