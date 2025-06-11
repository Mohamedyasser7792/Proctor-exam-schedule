<?php

namespace App\Exports;

use App\Models\TeachingAssistant;
use App\Models\ExamScheduleTeachingAssistant;
use App\Models\ExamSchedule;
use App\Models\StudySubject;
use App\Models\StudyGroup;
use App\Models\ExamHall;
use App\Models\LastExamSchedule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class TeachingAssistantExport implements FromCollection
{
    protected $ta_id;

    public function __construct($ta_id)
    {
        $this->ta_id = $ta_id;
    }

    public function collection()
    {
        // Fetch data for the teaching assistant
        $teachingAssistant = TeachingAssistant::where('ta_id', $this->ta_id)->first();
        if (!$teachingAssistant) {
            return new Collection([['Error' => 'Teaching Assistant not found']]);
        }

        // Teaching Assistant Details Header
        $taHeader = ['Teaching Assistant Details'];
        $taDetails = [
            ['TA ID', 'Name', 'Status', 'Role', 'Join Date', 'Day Offs'],
            [
                $teachingAssistant->ta_id,
                $teachingAssistant->name,
                $teachingAssistant->status,
                $teachingAssistant->role,
                $teachingAssistant->join_date,
                $teachingAssistant->dayOffs->pluck('day_off')->join(', ')
            ]
        ];

        // Fetch related exam data
        $examIds = ExamScheduleTeachingAssistant::where('ta_id', $this->ta_id)->pluck('exam_id');
        $exams = ExamSchedule::whereIn('exam_id', $examIds)->get()->map(function ($exam) {
            $subject = StudySubject::where('subject_id', $exam->subject_id)->first();
            $group = StudyGroup::where('group_id', $exam->group_id)->first();

            // Fetch hall data from `last_exam_schedule` and `exam_halls`
            $lastExam = LastExamSchedule::where('exam_id', $exam->exam_id)->first();
            $hall = $lastExam ? ExamHall::where('hall_id', $lastExam->hall_id)->first() : null;

            return [
                'Exam ID' => $exam->exam_id,
                'Duration' => $exam->duration,
                'Exam Day' => $exam->exam_day,
                'Exam Date' => $exam->exam_date,
                'Subject Name' => $subject->subject_name ?? null,
                'Group Name' => $group->group_name ?? null,
                'Number of Groups' => $group->number_of_groups ?? null,
                'Hall Name' => $hall->hall_name ?? null,
                'Number of Students in Hall' => $hall->number_of_students ?? null,
                'Start Time' => $exam->start_time ?? null,
                'End Time' => $exam->end_time ?? null,
            ];
        });

        // Exam Details Header
        $examHeader = [
            '',
            'Exam Details',
            '',
        ];
        $examColumns = [
            'Exam ID', 'Duration', 'Exam Day', 'Exam Date', 
            'Subject Name', 'Group Name', 'Number of Groups',
            'Hall Name', 'Number of Students in Hall',
            'Start Time', 'End Time'
        ];

        // Combine all data
        $data = new Collection([
            $taHeader, 
            ...$taDetails, 
            [''], 
            $examHeader, 
            $examColumns,
        ]);

        // Merge exams into the data
        $data = $data->merge($exams);

        return $data;
    }
}
