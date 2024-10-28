<?php

namespace App\Services\Customer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

use App\Models\Customer\Classes;
use App\Models\Courses;
use App\Models\CalendarLearn;
use App\Models\Customer\ClassOpening;

class ClassService
{
    public function store($request)
    {
        $dateEnd = $this->calculateDateEnd($request['date_start'], $request['course_id'], $request['calendar_learn_id']);

        $create = [
            'name'               => $request['name'],
            'campuse_id'         => $request['campuse_type'] == 2 ? $request['campuse_id'] : 0,
            'class_id'           => $request['class_id'] ?? 0,
            'class_url'          => $request['class_url'],
            'course_category_id' => $request['course_category_id'],
            'course_id'          => $request['course_id'],
            'day_shift_learn_id' => $request['day_shift_learn_id'],
            'calendar_learn_id'  => $request['calendar_learn_id'],
            'time_study_id'      => $request['time_study_id'],
            'date_start'         => $request['date_start'],
            'date_end'           => $dateEnd,
            'user_admission_id'  => $request['user_admission_id'],
            'note'               => $request['note'],
        ];

        $data = Classes::create($create);

        return $data;
    }

    private function calculateDateEnd($dateStart, $courseId, $calendarLearnId)
    {
        $course = Courses::find($courseId);
        $calendar = CalendarLearn::find($calendarLearnId); 

        $daysOfWeek = $calendar->days; // ngày học trong tuần bắt đầu từ thứ 2 [0, 1, 2, 3, 4, 5, 6]
        $numberCourse = $course->number_course; // số buổi học trong 1 khóa học
        
        $date = Carbon::parse($dateStart);
        $count = 0;
    
        while ($count < $numberCourse) {
            if (in_array($date->dayOfWeek, $daysOfWeek)) {
                $count++;
            }
            
            if ($count == $numberCourse) {
                break;
            }
            
            $date->addDay();
        }
    
        return $date->format('Y-m-d');
    }

    public function addOpening($request)
    {
        $class = Classes::find($request['class_id']);

        if (!$class) {
            throw new \Exception('Không tìm thấy lớp học!');
            return;
        }

        $add = [
            'date_opening_old' => $class->date_start,
            'date_opening_new' => Carbon::parse($request['date_opening_new'])->format('Y-m-d'),
            'days' => Carbon::parse($request['date_opening_new'])->diffInDays($class->date_start),
            'class_id' => $request['class_id'],
            'note' => $request['note'],
        ];

        $ClassOpening = ClassOpening::create($add);

        $dateEnd = $this->calculateDateEnd($request['date_opening_new'], $class->course_id, $class->calendar_learn_id);

        $class->update([
            'date_start' => Carbon::parse($request['date_opening_new'])->format('Y-m-d'),
            'date_end' => $dateEnd,
            'status' => 2
        ]);

        $ClassOpening->date_end = $dateEnd;

        return $ClassOpening;
    }
}
