<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceUser extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'date',
    ];
}
