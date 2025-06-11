<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamHall extends Model
{
    use HasFactory;

    protected $table = 'exam_halls';
    protected $primaryKey = 'hall_id';
    public $timestamps = false;

    protected $fillable = [
        'hall_name',
        'number_of_students',
    ];

    public function examSchedules()
    {
        return $this->hasMany(ExamSchedule::class, 'hall_id', 'hall_id');
    }
}
