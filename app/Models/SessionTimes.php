<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionTimes extends Model
{
    protected $fillable = [
        'session_id',
        'from',
        'to',
        'day',
    ];
}
