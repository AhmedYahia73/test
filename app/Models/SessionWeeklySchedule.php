<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionWeeklySchedule extends Model
{
    protected $fillable = [
        'session_id',
        'day',
        'from',
        'to',
    ]; 
}
