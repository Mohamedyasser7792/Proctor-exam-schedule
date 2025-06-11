<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudySubject extends Model
{
    use HasFactory;

    protected $table = 'study_subjects';
    protected $primaryKey = 'subject_id';
    public $timestamps = true;

    protected $fillable = [
        'subject_name',
        'group_id'
    ];

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class, 'group_id', 'group_id');
    }

    public function examSchedules()
    {
        return $this->hasMany(ExamSchedule::class, 'subject_id', 'subject_id');
    }
}
