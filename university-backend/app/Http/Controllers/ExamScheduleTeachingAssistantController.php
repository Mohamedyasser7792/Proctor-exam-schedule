<?php

namespace App\Http\Controllers;

use App\Models\ExamScheduleTeachingAssistant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExamScheduleTeachingAssistantController extends Controller
{
    /**
     * List all data in the exam_schedule_teaching_assistants table.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $assignments = ExamScheduleTeachingAssistant::with(['examSchedule', 'teachingAssistant'])->get();
            return response()->json($assignments);
        } catch (\Exception $e) {
            Log::error('Error fetching TA assignments: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching TA assignments'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'exam_id' => 'required|exists:exam_schedule,exam_id',
                'ta_id' => 'required|exists:teaching_assistants,ta_id'
            ]);

            $assignment = ExamScheduleTeachingAssistant::create($request->all());
            return response()->json($assignment, 201);
        } catch (\Exception $e) {
            Log::error('Error creating TA assignment: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating TA assignment'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $assignment = ExamScheduleTeachingAssistant::findOrFail($id);
            $assignment->delete();
            return response()->json(['message' => 'TA assignment deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting TA assignment: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting TA assignment'], 500);
        }
    }
}
