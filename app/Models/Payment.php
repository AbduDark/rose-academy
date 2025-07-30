<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
    'user_id', 'course_id', 'transaction_number', 'phone_number', 'status'
];
}
