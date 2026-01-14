<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LiveSession extends Model
{
    protected $fillable = [
        'name',
        'teacher_id',
        'start_date',
        'end_date',
    ]; 

    public function teacher()
    {
        return $this->belongsTo(User::class, "teacher_id");
    }

    public function sessionWeeklySchedule()
    {
        return $this->hasMany(SessionWeeklySchedule::class, "session_id");
    }

    public function students()
    {
        return $this->belongsToMany(User::class, "session_user", "session_id", "user_id");
    }

    public function actualSessions(){
        return $this->hasMany(SessionTimes::class, "session_id")
        ->orderBy('date')
        ->orderBy('from');
    }
    
    public function generateActualDates()
    {
        $arr = [];
        $weeklyRules = $this->sessionWeeklySchedule; 

        $this->actualSessions()->delete();

        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        
        foreach ($weeklyRules as $slot) {
            $dayName = $slot->day; // التأكد من أن الحقل اسمه day في جدول WeeklySchedule
            $startTimeStr = $slot->from;
            $endTimeStr = $slot->to;

            $period = CarbonPeriod::between($startDate, $endDate)->addFilter(function ($date) use ($dayName) {
                return strtolower($date->dayName) === strtolower($dayName);
            });
            $arr[] = $period;

            foreach ($period as $date) {
                $parsedStart = Carbon::parse($startTimeStr);
                $parsedEnd = Carbon::parse($endTimeStr);
                
                $startAt = $date->copy()->setTimeFrom($parsedStart);
                $endAt = $date->copy()->setTimeFrom($parsedEnd);

                if ($parsedEnd->lt($parsedStart)) {
                    $endAt->addDay(); 
                }

                $arr[] =$this->actualSessions()->create([
                    'date' => $date->format('Y-m-d'), 
                    'from' => $startAt,
                    'to'   => $endAt, 
                    'day'  => $date->format('l'),
                ]);
            }
        }

        return $arr;
    }
}
