<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudyGroupController extends Controller
{
    // List all study groups
    public function index()
    {
        return StudyGroup::with('subgroups')->get();
    }

    // Create a new study group
    public function store(Request $request)
    {
        try {
        $request->validate([
                'group_name' => 'required|string|max:100',
                'number_of_groups' => 'required|integer|min:1'
            ]);

            DB::beginTransaction();

            $studyGroup = StudyGroup::create([
                'group_name' => $request->group_name,
                'number_of_groups' => $request->number_of_groups
            ]);

            DB::commit();
            
            return response()->json([
                'message' => 'Study Group created successfully',
                'data' => $studyGroup->load('subgroups')
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating study group: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error creating study group',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Update a study group by ID
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'group_name' => 'required|string|max:100',
                'number_of_groups' => 'required|integer|min:1'
            ]);

            DB::beginTransaction();

        $studyGroup = StudyGroup::findOrFail($id);
            
            $studyGroup->update([
                'group_name' => $request->group_name,
                'number_of_groups' => $request->number_of_groups
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Study Group updated successfully',
                'data' => $studyGroup->load('subgroups')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating study group: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error updating study group',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a study group by ID
    public function destroy($id)
    {
        try {
            $studyGroup = StudyGroup::findOrFail($id);
            $studyGroup->delete();

        return response()->json(['message' => 'Study Group deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting study group: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error deleting study group',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyAll()
    {
        try {
            StudyGroup::query()->delete();
            return response()->json(['message' => 'All Study Groups have been deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting all study groups: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error deleting all study groups',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
