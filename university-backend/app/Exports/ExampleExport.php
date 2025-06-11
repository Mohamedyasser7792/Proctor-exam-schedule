<?php

namespace App\Exports;

use App\Models\LastExamSchedule;
use App\Models\ExamSchedule;
use App\Models\StudySubject;
use App\Models\StudyGroup;
use App\Models\Subgroup;
use App\Models\ExamHall;
use App\Models\TeachingAssistant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExampleExport implements FromCollection
{
    public function collection()
    {
        // Step 1: Define the header row
        $header = [
            'Exam ID', 
            'Duration', 
            'Exam Day', 
            'Exam Date', 
            'Subject Name', 
            'Group Name', 
            'Number of Groups', 
            'Subgroup Name', 
            'Subgroup Capacity', 
            'Hall Name', 
            'Number of Students in Hall', 
            'Teaching Assistants', 
            'Start Time', 
            'End Time', 
            'Exam Date'
        ];

        // Step 2: Get data from `last_exam_schedule`
        $lastExamSchedules = LastExamSchedule::all();

        $data = $lastExamSchedules->map(function ($schedule) {
            // Step 3: Fetch related data from other tables
            $examSchedule = ExamSchedule::where('exam_id', $schedule->exam_id)->first();
            $subject = StudySubject::where('subject_id', $examSchedule->subject_id)->first();
            $group = StudyGroup::where('group_id', $subject->group_id)->first();
            $subgroup = Subgroup::where('subgroup_id', $schedule->subgroup_id)->first();
            $examHall = ExamHall::where('hall_id', $schedule->hall_id)->first();

            // Step 4: Process `ta_ids` JSON column
            $taIds = json_decode($schedule->ta_ids, true) ?? [];
            $taNames = TeachingAssistant::whereIn('ta_id', $taIds)->pluck('name')->toArray();

            // Step 5: Map the data into an array
            return [
                $schedule->exam_id,
                $examSchedule->duration ?? null,
                $examSchedule->exam_day ?? null,
                $examSchedule->exam_date ?? null,
                $subject->subject_name ?? null,
                $group->group_name ?? null,
                $group->number_of_groups ?? null,
                $subgroup->name ?? null,
                $subgroup->capacity ?? null,
                $examHall->hall_name ?? null,
                $examHall->number_of_students ?? null,
                json_encode($taNames),
                $schedule->start_time,
                $schedule->end_time,
                $schedule->exam_date,
            ];
        });

        // Step 6: Add the header to the data
        $data->prepend($header);

        return new Collection($data);
    }
}
