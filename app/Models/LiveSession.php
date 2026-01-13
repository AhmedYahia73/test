<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveSession extends Model
{
    protected $fillable = [
        'name',
        'teacher_id',
        'link',
        'date_link',
        'student_count',
    ];

    public function sessionTimes()
    {
        return $this->hasMany(SessionTimes::class, "session_id");
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, "teacher_id");
    }

    public function students()
    {
        return $this->belongsToMany(User::class, "session_user", "session_id", "user_id");
    }
    
    public function students_attendance()
    {
        return $this->belongsToMany(User::class, "attendance_user", "session_id", "user_id");
    }
}
