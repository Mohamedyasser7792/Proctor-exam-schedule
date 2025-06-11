<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $table = 'exam_schedule';
    protected $primaryKey = 'exam_id';
    public $timestamps = true;

    protected $fillable = [
        'exam_day',
        'exam_date',
        'subject_id',
        'group_id',
        'start_time',
        'end_time',
        'duration'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration' => 'integer'
    ];

    // Relationships
    public function subject()
    {
        return $this->belongsTo(StudySubject::class, 'subject_id', 'subject_id');
    }

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class, 'group_id', 'group_id');
    }

    public function teachingAssistants()
    {
        return $this->belongsToMany(TeachingAssistant::class, 'exam_schedule_teaching_assistants', 'exam_id', 'ta_id');
    }

    public function lastExamSchedule()
    {
        return $this->hasOne(LastExamSchedule::class, 'exam_id', 'exam_id');
    }

    // Clear hall_id and subgroup_id fields
    public static function clearHallAndSubgroup()
    {
        self::query()->update([
            'hall_id' => null,
            'subgroup_id' => null,
        ]);
    }
}
