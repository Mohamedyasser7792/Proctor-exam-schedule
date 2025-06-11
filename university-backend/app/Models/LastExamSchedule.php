<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastExamSchedule extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's naming convention
    protected $table = 'last_exam_schedule';
    protected $primaryKey = 'schedule_id';
    public $timestamps = true;

    // Specify fillable fields for mass assignment
    protected $fillable = [
        'schedule_data',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'schedule_data' => 'array'
    ];
}
