<?php

namespace App\Http\Controllers;

use App\Models\ExamHall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamHallController extends Controller
{
    // List all exam halls
    public function index()
    {
        return ExamHall::all();
    }

    // Create a new exam hall
    public function store(Request $request)
    {
        try {
        $request->validate([
                'hall_name' => 'required|string|max:100',
                'number_of_students' => 'required|integer|min:1'
            ]);

            DB::beginTransaction();

            $examHall = ExamHall::create([
                'hall_name' => $request->hall_name,
                'number_of_students' => $request->number_of_students
            ]);

            DB::commit();
            
            return response()->json([
                'message' => 'Exam Hall created successfully',
                'data' => $examHall
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating exam hall: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error creating exam hall',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Update an exam hall by ID
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'hall_name' => 'required|string|max:100',
                'number_of_students' => 'required|integer|min:1'
            ]);

            DB::beginTransaction();

        $examHall = ExamHall::findOrFail($id);
            
            $examHall->update([
                'hall_name' => $request->hall_name,
                'number_of_students' => $request->number_of_students
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Exam Hall updated successfully',
                'data' => $examHall
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating exam hall: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error updating exam hall',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Delete an exam hall by ID
    public function destroy($id)
    {
        try {
            $examHall = ExamHall::findOrFail($id);
            $examHall->delete();

        return response()->json(['message' => 'Exam Hall deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting exam hall: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error deleting exam hall',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyAll()
    {
        try {
            ExamHall::query()->delete();
            return response()->json(['message' => 'All Exam Halls have been deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting all exam halls: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error deleting all exam halls',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
