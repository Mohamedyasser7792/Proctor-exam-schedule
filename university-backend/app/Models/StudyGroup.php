<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyGroup extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'group_id';
    
    protected $fillable = [
        'group_name',
        'number_of_groups'
    ];

    public function examSchedules()
    {
        return $this->hasMany(ExamSchedule::class, 'group_id', 'group_id');
    }

    public function subgroups()
    {
        return $this->hasMany(Subgroup::class, 'group_id', 'group_id');
    }
}
