<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamScheduleTeachingAssistant extends Model
{
    use HasFactory;

    protected $table = 'exam_schedule_teaching_assistants';
    public $timestamps = true;

    protected $fillable = [
        'exam_id',
        'ta_id',
        'created_at',
        'updated_at'
    ];

    public function examSchedule()
    {
        return $this->belongsTo(ExamSchedule::class, 'exam_id', 'exam_id');
    }

    public function teachingAssistant()
    {
        return $this->belongsTo(TeachingAssistant::class, 'ta_id', 'ta_id');
    }

    // Method to clear all data in the table
    public static function clearAll()
    {
        self::query()->delete();
    }
}
