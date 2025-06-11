<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulingError extends Model
{
    use HasFactory;

    protected $table = 'scheduling_errors';
    protected $primaryKey = 'error_id';
    public $timestamps = true;

    protected $fillable = [
        'group_id',
        'subject_id',
        'error_message',
        'created_at',
        'updated_at'
    ];

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class, 'group_id', 'group_id');
    }

    public function studySubject()
    {
        return $this->belongsTo(StudySubject::class, 'subject_id', 'subject_id');
    }

    // Clear all data in the scheduling_errors table
    public static function clearAll()
    {
        self::query()->truncate();
    }
}
