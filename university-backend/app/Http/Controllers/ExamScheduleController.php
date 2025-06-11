<?php

namespace App\Http\Controllers;

use App\Models\ExamSchedule;
use App\Models\StudySubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Exports\ExamScheduleExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExampleExport;
use Symfony\Component\HttpFoundation\Response;

class ExamScheduleController extends Controller
{
    // Create a new exam schedule
    public function store(Request $request)
    {
        try {
            $request->validate([
                'exam_day' => 'required|string|max:50',
                'exam_date' => 'required|date',
                'subject_id' => 'required|exists:study_subjects,subject_id',
                'group_id' => 'required|exists:study_groups,group_id',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'duration' => 'required|integer|min:1'
            ]);

            DB::beginTransaction();

            $schedule = ExamSchedule::create([
                'exam_day' => $request->exam_day,
                'exam_date' => $request->exam_date,
                'subject_id' => $request->subject_id,
                'group_id' => $request->group_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration' => $request->duration
            ]);

            DB::commit();
            
            return response()->json([
                'message' => 'Exam schedule created successfully',
                'data' => $schedule
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating exam schedule: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error creating exam schedule',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Update a specific exam schedule by ID
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'exam_day' => 'required|string|max:50',
                'exam_date' => 'required|date',
                'subject_id' => 'required|exists:study_subjects,subject_id',
                'group_id' => 'required|exists:study_groups,group_id',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'duration' => 'required|integer|min:1'
            ]);

            DB::beginTransaction();

            $schedule = ExamSchedule::findOrFail($id);
            
            $schedule->update([
                'exam_day' => $request->exam_day,
                'exam_date' => $request->exam_date,
                'subject_id' => $request->subject_id,
                'group_id' => $request->group_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration' => $request->duration
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Exam schedule updated successfully',
                'data' => $schedule
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating exam schedule: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error updating exam schedule',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // List all exam schedules
    public function index()
    {
        try {
            $schedules = ExamSchedule::with(['subject', 'studyGroup', 'teachingAssistants'])->get();
            return response()->json($schedules);
        } catch (\Exception $e) {
            Log::error('Error fetching exam schedules: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error fetching exam schedules',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Get a specific exam schedule by ID
    public function show($id)
    {
        try {
            $schedule = ExamSchedule::with(['subject', 'studyGroup', 'teachingAssistants'])
                ->findOrFail($id);
            return response()->json($schedule);
        } catch (\Exception $e) {
            Log::error('Error fetching exam schedule: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error fetching exam schedule',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a specific exam schedule by ID
    public function destroy($id)
    {
        try {
            $schedule = ExamSchedule::findOrFail($id);
            $schedule->delete();

            return response()->json(['message' => 'Exam schedule deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting exam schedule: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error deleting exam schedule',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyAll()
    {
        try {
            DB::beginTransaction();
            
            // Delete all exam schedules
            ExamSchedule::query()->delete();
            
            DB::commit();
            
            return response()->json(['message' => 'All exam schedules have been deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting all exam schedules: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error deleting all exam schedules',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        $fileName = 'last_exam_schedule_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
    
        return Excel::download(new ExampleExport, $fileName, \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
