<?php

namespace App\Http\Controllers;

use App\Models\ExamSchedule;
use App\Models\LastExamSchedule;
use App\Models\SchedulingError;
use App\Models\ExamScheduleTeachingAssistant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupController extends Controller
{
    /**
     * Clear specific data from multiple tables.
     *
     * @return JsonResponse
     */
    public function clearData(Request $request)
    {
        try {
            // Disable foreign key checks temporarily using raw SQL
            DB::unprepared('SET FOREIGN_KEY_CHECKS=0');

            // Clear only the specified tables in the correct order
            DB::table('subgroup')->truncate();
            DB::table('exam_schedule_teaching_assistants')->truncate();
            DB::table('last_exam_schedule')->truncate();
            DB::table('scheduling_errors')->truncate();

            // Re-enable foreign key checks
            DB::unprepared('SET FOREIGN_KEY_CHECKS=1');

            return response()->json(['message' => 'Specified tables cleared successfully']);
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::unprepared('SET FOREIGN_KEY_CHECKS=1');
            
            Log::error('Error clearing data: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Error clearing data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
