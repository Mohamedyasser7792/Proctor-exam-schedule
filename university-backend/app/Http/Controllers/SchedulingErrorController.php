<?php

namespace App\Http\Controllers;

use App\Models\SchedulingError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SchedulingErrorController extends Controller
{
    /**
     * List all scheduling errors.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $errors = SchedulingError::with(['studyGroup', 'studySubject'])->get();
            return response()->json($errors);
        } catch (\Exception $e) {
            Log::error('Error fetching scheduling errors: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching scheduling errors'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'group_id' => 'required|exists:study_groups,group_id',
                'subject_id' => 'required|exists:study_subjects,subject_id',
                'error_message' => 'required|string'
            ]);

            $error = SchedulingError::create($request->all());
            return response()->json($error, 201);
        } catch (\Exception $e) {
            Log::error('Error creating scheduling error: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating scheduling error'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $error = SchedulingError::findOrFail($id);
            $error->delete();
            return response()->json(['message' => 'Scheduling error deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting scheduling error: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting scheduling error'], 500);
        }
    }
}
