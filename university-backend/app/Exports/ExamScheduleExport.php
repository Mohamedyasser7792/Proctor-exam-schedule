<?php

namespace App\Exports;

use App\Models\ExamSchedule;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExamScheduleExport implements FromCollection
{
    public function collection()
    {
        return ExamSchedule::all();
    }
}

