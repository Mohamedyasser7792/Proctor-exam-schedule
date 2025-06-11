<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subgroup extends Model
{
    use HasFactory;

    protected $table = 'subgroup';

    protected $primaryKey = 'subgroup_id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'group_id',
        'capacity',
        'created_at',
        'updated_at'
    ];

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class, 'group_id', 'group_id');
    }

    public function examSchedules()
    {
        return $this->hasMany(ExamSchedule::class, 'subgroup_id', 'subgroup_id');
    }

    // Method to clear all data in the table
    public static function clearAll()
    {
        self::query()->delete();
    }
}
