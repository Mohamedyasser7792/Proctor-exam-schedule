<?php

namespace App\Http\Controllers;

use App\Models\LastExamSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LatestExamScheduleController extends Controller
{
    /**
     * List all exam schedules with hall and subgroup details.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $latestSchedule = LastExamSchedule::latest()->first();
            
            if (!$latestSchedule) {
                return response()->json([
                    'data' => [],
                    'message' => 'No schedule found'
                ]);
            }

            return response()->json([
                'data' => $latestSchedule->schedule_data,
                'message' => 'Schedule retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching latest schedule: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error fetching latest schedule',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'schedule_data' => 'required|array'
            ]);

            $schedule = LastExamSchedule::create($request->all());
            return response()->json($schedule, 201);
        } catch (\Exception $e) {
            Log::error('Error creating latest schedule: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating latest schedule'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = LastExamSchedule::findOrFail($id);
            $schedule->delete();
            return response()->json(['message' => 'Latest schedule deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting latest schedule: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting latest schedule'], 500);
        }
    }
}
