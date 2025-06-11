<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaDayOff extends Model
{
    protected $table = 'ta_day_offs';

    protected $fillable = ['ta_id', 'day_off'];

    public $timestamps = true;

    public function teachingAssistant()
    {
        return $this->belongsTo(TeachingAssistant::class, 'ta_id');
    }
}
