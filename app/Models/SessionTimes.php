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
        'date',
        'link',
        'teacher_id',
        'tacher_entered',
    ]; 

    public function session()
    {
        return $this->belongsTo(LiveSession::class, "session_id");
    }

    public function students_attendance()
    {
        return $this->belongsToMany(User::class, "attendance_student", "session_times_id", "user_id");
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, "teacher_id");
    }
}
