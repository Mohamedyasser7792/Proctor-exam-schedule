<?php

namespace App\Http\Controllers;

use App\Models\StudySubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudySubjectController extends Controller
{
    // List all study subjects
    public function index()
    {
        try {
            $subjects = StudySubject::with('studyGroup')->get();
            return response()->json($subjects);
        } catch (\Exception $e) {
            Log::error('Error fetching study subjects: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error fetching study subjects',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Create a new study subject
    public function store(Request $request)
    {
        try {
        $request->validate([
                'subject_name' => 'required|string|max:100',
                'group_id' => 'required|exists:study_groups,group_id'
        ]);

            DB::beginTransaction();

            $subject = StudySubject::create([
                'subject_name' => $request->subject_name,
                'group_id' => $request->group_id
            ]);

            DB::commit();
            
            return response()->json([
                'message' => 'Study subject created successfully',
                'data' => $subject
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating study subject: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error creating study subject',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Update a study subject by ID
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'subject_name' => 'required|string|max:100',
                'group_id' => 'required|exists:study_groups,group_id'
            ]);

            DB::beginTransaction();

            $subject = StudySubject::findOrFail($id);
            
            $subject->update([
                'subject_name' => $request->subject_name,
                'group_id' => $request->group_id
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Study subject updated successfully',
                'data' => $subject
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating study subject: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error updating study subject',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a study subject by ID
    public function destroy($id)
    {
        try {
            $subject = StudySubject::findOrFail($id);
            $subject->delete();

            return response()->json(['message' => 'Study subject deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting study subject: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error deleting study subject',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyAll()
    {
        try {
            StudySubject::query()->delete();
            return response()->json(['message' => 'All study subjects have been deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting all study subjects: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error deleting all study subjects',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
