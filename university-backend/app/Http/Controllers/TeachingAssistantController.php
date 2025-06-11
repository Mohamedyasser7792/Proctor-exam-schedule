<?php

namespace App\Http\Controllers;

use App\Models\TeachingAssistant;
use App\Models\ExamScheduleTeachingAssistant;
use App\Models\ExamSchedule;
use App\Models\StudySubject;
use App\Models\StudyGroup;
use App\Exports\TeachingAssistantExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class TeachingAssistantController extends Controller
{
    public function index()
    {
        return TeachingAssistant::with('dayOffs')->get();
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'status' => 'required|in:Basic,Reserve',
                'role' => 'required|in:Teaching Assistant,Doctor',
                'join_date' => 'required|date',
                'day_offs' => 'required|array|min:1',
                'day_offs.*' => 'required|date|date_format:Y-m-d'
            ]);

            DB::beginTransaction();

            $teachingAssistant = TeachingAssistant::create([
                'name' => $request->name,
                'status' => $request->status,
                'role' => $request->role,
                'join_date' => $request->join_date,
                'assignments_count' => 0
            ]);

            // Create day offs
            $dayOffs = collect($request->day_offs)->map(function($dayOff) {
                return [
                    'day_off' => Carbon::parse($dayOff)->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            $teachingAssistant->dayOffs()->createMany($dayOffs);

            DB::commit();
            
            return response()->json([
                'message' => 'Teaching Assistant created successfully',
                'data' => $teachingAssistant->load('dayOffs')
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating teaching assistant: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error creating teaching assistant',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'status' => 'required|in:Basic,Reserve',
                'role' => 'required|in:Teaching Assistant,Doctor',
                'join_date' => 'required|date',
                'day_offs' => 'nullable|array',
                'day_offs.*' => 'date'
            ]);

            DB::beginTransaction();

            $teachingAssistant = TeachingAssistant::findOrFail($id);
            
            $teachingAssistant->update([
                'name' => $request->name,
                'status' => $request->status,
                'role' => $request->role,
                'join_date' => $request->join_date
            ]);

            // Delete existing day offs
            $teachingAssistant->dayOffs()->delete();

            // Add new day offs
            if ($request->has('day_offs') && is_array($request->day_offs)) {
                foreach ($request->day_offs as $dayOff) {
                    $teachingAssistant->dayOffs()->create([
                        'day_off' => $dayOff
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Teaching Assistant updated successfully',
                'data' => $teachingAssistant->load('dayOffs')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating teaching assistant: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error updating teaching assistant',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $ta = TeachingAssistant::findOrFail($id);
        $ta->delete();

        return response()->json(['message' => 'Teaching Assistant deleted successfully']);
    }

    public function destroyAll()
    {
        TeachingAssistant::query()->delete();

        return response()->json(['message' => 'All Teaching Assistants have been deleted successfully']);
    }

    public function getTeachingAssistantDetails($ta_id)
    {
        $ta = TeachingAssistant::with('dayOffs')->where('ta_id', $ta_id)->first();

        if (!$ta) {
            return response()->json(['error' => 'Teaching Assistant not found'], 404);
        }

        $examIds = ExamScheduleTeachingAssistant::where('ta_id', $ta_id)->pluck('exam_id');

        $exams = ExamSchedule::whereIn('exam_id', $examIds)->get()->map(function ($exam) {
            $subject = StudySubject::where('subject_id', $exam->subject_id)->first();
            $group = StudyGroup::where('group_id', $exam->group_id)->first();

            return [
                'exam_id' => $exam->exam_id,
                'duration' => $exam->duration,
                'exam_day' => $exam->exam_day,
                'exam_date' => $exam->exam_date,
                'subject_name' => $subject->subject_name ?? null,
                'group_name' => $group->group_name ?? null,
                'number_of_groups' => $group->number_of_groups ?? null,
            ];
        });

        return response()->json([
            'ta_id' => $ta->ta_id,
            'name' => $ta->name,
            'status' => $ta->status,
            'day_offs' => $ta->dayOffs->pluck('day_off'),
            'day_off_weekdays' => $ta->dayOffs->map(fn($d) => Carbon::parse($d->day_off)->format('l')),
            'exams' => $exams,
        ]);
    }

    public function exportTeachingAssistantDetails($ta_id)
    {
        $fileName = "teaching_assistant_{$ta_id}_details.xlsx";
        Excel::store(new TeachingAssistantExport($ta_id), $fileName, 'local');
        $filePath = storage_path("app/{$fileName}");

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}



































































// namespace App\Http\Controllers;

// use App\Models\TeachingAssistant;
// use App\Models\AuthorizedAbsenceDay;
// use Illuminate\Http\Request;
// use App\Models\ExamScheduleTeachingAssistant;
// use App\Models\ExamSchedule;
// use App\Models\StudySubject;
// use App\Models\StudyGroup;

// use App\Exports\TeachingAssistantExport;
// use Maatwebsite\Excel\Facades\Excel;
// use Symfony\Component\HttpFoundation\Response;
// use Illuminate\Support\Carbon;

// class TeachingAssistantController extends Controller
// {
//     // List all teaching assistants
//     public function index()
//     {
//         return TeachingAssistant::all();
//     }

//     // Create a new teaching assistant with day-off limit validation
//     // public function store(Request $request)
//     // {
//     //     $request->validate([
//     //         'name' => 'required|string',
//     //         'day_off' => 'nullable|date',
//     //         'status' => 'nullable|string|in:Basic,Reserve',
//     //     ]);

//     //     $dayOff = $request->day_off;

//     //     // Get the allowed number of repetitions from the AuthorizedAbsenceDay table
//     //     $authorizedDays = AuthorizedAbsenceDay::first();
//     //     $maxDays = $authorizedDays ? $authorizedDays->number_of_days : 0;

        
//     //     $dayOffDate = Carbon::parse($dayOff); // parse input date
//     //     $dayOffWeekday = $dayOffDate->format('l'); // e.g., 'Monday'

//     //     // Count current entries that fall on the same weekday
//     //     $currentCount = TeachingAssistant::whereRaw('DAYNAME(day_off) = ?', [$dayOffWeekday])->count();


//     //     // Check if the day_off exceeds the allowed number
//     //     if ($currentCount >= $maxDays) {
//     //         return response()->json([
//     //             'error' => "The maximum limit for selecting $dayOff has been reached."
//     //         ], 400);
//     //     }

//     //     // Create the teaching assistant if the limit is not exceeded
//     //     return TeachingAssistant::create($request->all());

        
//     // }


//     public function store(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string',
//             'status' => 'nullable|string|in:Basic,Reserve',
//             'day_offs' => 'required|array|min:1',
//             'day_offs.*' => 'required|date',
//         ]);

//         $authorizedDays = AuthorizedAbsenceDay::first();
//         $maxDays = $authorizedDays ? $authorizedDays->number_of_days : 0;

//         // Check if adding any of the selected weekdays exceeds the allowed limit
//         foreach ($request->day_offs as $dayOff) {
//             $weekday = Carbon::parse($dayOff)->format('l');
//             $count = DB::table('ta_day_offs')
//                 ->whereRaw('DAYNAME(day_off) = ?', [$weekday])
//                 ->count();

//             if ($count >= $maxDays) {
//                 return response()->json([
//                     'error' => "The maximum limit for selecting $weekday as a day off has been reached."
//                 ], 400);
//             }
//         }

//         // Create the TA
//         $ta = TeachingAssistant::create([
//             'name' => $request->name,
//             'status' => $request->status,
//         ]);

//         // Store day offs
//         foreach ($request->day_offs as $dayOff) {
//             $ta->dayOffs()->create([
//                 'day_off' => Carbon::parse($dayOff)->toDateString(),
//             ]);
//         }

//         return response()->json($ta->load('dayOffs'), 201);
//     }



//     // Update a teaching assistant by ID
//     // public function update(Request $request, $id)
//     // {
//     //     $request->validate([
//     //         'name' => 'required|string',
//     //         'day_off' => 'nullable|date',
//     //         'status' => 'nullable|string|in:Basic,Reserve',
//     //     ]);

//     //     $teachingAssistant = TeachingAssistant::findOrFail($id);
//     //     $teachingAssistant->update($request->all());

//     //     return $teachingAssistant;
//     // }


//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'name' => 'required|string',
//             'status' => 'nullable|string|in:Basic,Reserve',
//             'day_offs' => 'required|array|min:1',
//             'day_offs.*' => 'required|date',
//         ]);

//         $authorizedDays = AuthorizedAbsenceDay::first();
//         $maxDays = $authorizedDays ? $authorizedDays->number_of_days : 0;

//         // Check limit for each proposed weekday, ignoring the TA's current entries
//         foreach ($request->day_offs as $dayOff) {
//             $weekday = Carbon::parse($dayOff)->format('l');
//             $count = DB::table('ta_day_offs')
//                 ->whereRaw('DAYNAME(day_off) = ?', [$weekday])
//                 ->count();

//             // Subtract current TA's existing entries for this weekday
//             $ta = TeachingAssistant::findOrFail($id);
//             $taWeekdayCount = $ta->dayOffs()
//                 ->whereRaw('DAYNAME(day_off) = ?', [$weekday])
//                 ->count();

//             if (($count - $taWeekdayCount) >= $maxDays) {
//                 return response()->json([
//                     'error' => "The maximum limit for selecting $weekday as a day off has been reached."
//                 ], 400);
//             }
//         }

//         $ta = TeachingAssistant::findOrFail($id);
//         $ta->update([
//             'name' => $request->name,
//             'status' => $request->status,
//         ]);

//         // Replace day offs
//         $ta->dayOffs()->delete();
//         foreach ($request->day_offs as $dayOff) {
//             $ta->dayOffs()->create([
//                 'day_off' => Carbon::parse($dayOff)->toDateString(),
//             ]);
//         }

//         return response()->json($ta->load('dayOffs'));
//     }




//     // Delete a teaching assistant by ID
//     public function destroy($id)
//     {
//         $teachingAssistant = TeachingAssistant::findOrFail($id);
//         $teachingAssistant->delete();

//         return response()->json(['message' => 'Teaching Assistant deleted successfully']);
//     }




//     public function destroyAll()
//     {
//         TeachingAssistant::query()->delete();
    
//         return response()->json(['message' => 'All Teaching Assistants have been deleted successfully']);
//     }
    
    


// //last api katch 


//     public function getTeachingAssistantDetails($ta_id)
//     {
//         // Fetch data from teaching_assistants table
//         $teachingAssistant = TeachingAssistant::where('ta_id', $ta_id)->first();

//         if (!$teachingAssistant) {
//             return response()->json(['error' => 'Teaching Assistant not found'], 404);
//         }

//         // Fetch related data from exam_schedule_teaching_assistants table
//         $examIds = ExamScheduleTeachingAssistant::where('ta_id', $ta_id)->pluck('exam_id');

//         if ($examIds->isEmpty()) {
//             return response()->json([
//                 'ta_id' => $ta_id,
//                 'name' => $teachingAssistant->name,
//                 'day_off' => $teachingAssistant->day_off,
//                 'status' => $teachingAssistant->status,
//                 'exams' => [],
//             ]);
//         }

//         // Fetch exam data from exam_schedule table
//         $exams = ExamSchedule::whereIn('exam_id', $examIds)->get()->map(function ($exam) {
//             $subject = StudySubject::where('subject_id', $exam->subject_id)->first();
//             $group = StudyGroup::where('group_id', $exam->group_id)->first();

//             return [
//                 'exam_id' => $exam->exam_id,
//                 'duration' => $exam->duration,
//                 'exam_day' => $exam->exam_day,
//                 'exam_date' => $exam->exam_date,
//                 'subject_name' => $subject->subject_name ?? null,
//                 'group_name' => $group->group_name ?? null,
//                 'number_of_groups' => $group->number_of_groups ?? null,
//             ];
//         });

//         // Return the data as JSON
//         // inside getTeachingAssistantDetails method:
//         return response()->json([
//             'ta_id' => $teachingAssistant->ta_id,
//             'name' => $teachingAssistant->name,
//             'day_off' => $teachingAssistant->day_off,
//             'day_off_weekday' => $teachingAssistant->day_off ? Carbon::parse($teachingAssistant->day_off)->format('l') : null,
//             'status' => $teachingAssistant->status,
//             'exams' => $exams,
//         ]);
//     }


//     public function exportTeachingAssistantDetails($ta_id)
//     {
//         $fileName = "teaching_assistant_{$ta_id}_details.xlsx";
    
//         // Generate the Excel file
//         Excel::store(new TeachingAssistantExport($ta_id), $fileName, 'local');
    
//         $filePath = storage_path("app/{$fileName}");
    
//         if (!file_exists($filePath)) {
//             return response()->json(['error' => 'File not found'], 404);
//         }
    
//         return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
//     }
    


// }

