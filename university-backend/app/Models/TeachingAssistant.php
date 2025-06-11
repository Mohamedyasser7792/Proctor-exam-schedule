<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class TeachingAssistant extends Model
// {
//     public $timestamps = false; // Disable timestamps

//     protected $fillable = [
//         'name',
//         'day_off',
//         'status',
//     ];

//     // Relationship to AuthorizedAbsenceDays
//     public function authorizedAbsenceDays()
//     {
//         return $this->hasOne(AuthorizedAbsenceDay::class);
//     }
// }




namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingAssistant extends Model
{
    protected $primaryKey = 'ta_id';

    protected $fillable = [
        'name',
        'status',
        'role',
        'join_date',
        'assignments_count'
    ];

    protected $casts = [
        'join_date' => 'date',
        'assignments_count' => 'integer'
    ];

    public $timestamps = true;

    public function dayOffs()
    {
        return $this->hasMany(TaDayOff::class, 'ta_id');
    }
}
