<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ExamSchedule;
use App\Models\TeachingAssistant;
use App\Models\ExamHall;
use Carbon\Carbon;
use App\Models\StudySubject;
use App\Models\StudyGroup;
use App\Models\SchedulingError;
use App\Models\LastExamSchedule;
use App\Models\Subgroup;
use App\Models\TADayOff;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GenerateController extends Controller
{
    private $rolePriority = [
        'Teaching Assistant' => 1,
        'Doctor' => 2
    ];

    private $statusPriority = [
        'basic' => 1,
        'backup' => 2
    ];

    private $cacheDuration = 3600; // 1 hour cache

    public function generate(Request $request)
    {
        try {
            DB::beginTransaction();

            // Clear existing data
            $this->clearExistingData();

            // Get all required data with caching
            $subjects = $this->getCachedData('subjects', function() {
                return StudySubject::all();
            });
            
            $studyGroups = $this->getCachedData('study_groups', function() {
                return StudyGroup::with('subgroups')->get();
            });
            
            $examHalls = $this->getCachedData('exam_halls', function() {
                return ExamHall::all();
            });
            
            $teachingAssistants = $this->getPrioritizedTeachingAssistants();

            // Validate data
            if (!$this->validateData($subjects, $studyGroups, $examHalls, $teachingAssistants)) {
                return response()->json(['error' => 'Insufficient data for scheduling'], 400);
            }

            // Generate subgroups based on study groups and hall capacities
            $this->generateSubgroups($studyGroups, $examHalls);

            // Refresh study groups with newly created subgroups
            $studyGroups = StudyGroup::with('subgroups')->get();

            // Partition study groups
            $partitions = $this->partitionStudyGroups($studyGroups, $examHalls);

            // Generate schedule for each partition
            $schedule = [];
            $generationStats = [
                'total_exams' => 0,
                'assigned_tas' => 0,
                'conflicts' => 0,
                'errors' => 0
            ];

            foreach ($partitions as $partition) {
                $partitionSchedule = $this->generatePartitionSchedule(
                    $partition['groups'],
                    $partition['hall'],
                    $subjects,
                    $teachingAssistants,
                    $generationStats
                );
                $schedule = array_merge($schedule, $partitionSchedule);
            }

            // Save schedule
            $this->saveSchedule($schedule);

            // Log generation statistics
            Log::info('Schedule generation completed', $generationStats);

            DB::commit();
            return response()->json([
                'message' => 'Schedule generated successfully',
                'statistics' => $generationStats
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Schedule generation error: ' . $e->getMessage());
            return response()->json(['error' => 'Error generating schedule: ' . $e->getMessage()], 500);
        }
    }

    private function getCachedData($key, $callback)
    {
        return Cache::remember($key, $this->cacheDuration, $callback);
    }

    private function getPrioritizedTeachingAssistants()
    {
        return TeachingAssistant::with(['dayOffs'])
            ->orderByRaw("FIELD(status, 'Basic', 'Reserve')")
            ->orderByRaw("FIELD(role, 'Teaching Assistant', 'Doctor')")
            ->orderBy('join_date', 'asc')
            ->get();
    }

    private function hasDayOff($ta, $examDay)
    {
        return $ta->dayOffs()
            ->where('day_off', $examDay)
            ->exists();
    }

    private function hasTimeConflict($ta, $examDay, $startTime, $endTime)
    {
        return ExamSchedule::where('ta_id', $ta->ta_id)
            ->where('exam_day', $examDay)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();
    }

    private function calculateExamTime($group, $subject, $hall)
    {
        $duration = $subject->duration;
        $startTime = Carbon::parse($group->start_time);
        $endTime = $startTime->copy()->addMinutes($duration);

        // Add buffer time between exams (15 minutes)
        $bufferTime = 15;

        // Check if the exam fits within the hall's operating hours
        $hallStartTime = Carbon::parse($hall->opening_time);
        $hallEndTime = Carbon::parse($hall->closing_time);

        if ($startTime->lt($hallStartTime)) {
            $startTime = $hallStartTime;
            $endTime = $startTime->copy()->addMinutes($duration);
        }

        if ($endTime->gt($hallEndTime)) {
            $endTime = $hallEndTime;
            $startTime = $endTime->copy()->subMinutes($duration);
        }

        return [
            'day' => $group->exam_day,
            'date' => $group->exam_date,
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
            'buffer_time' => $bufferTime
        ];
    }

    private function validateData($subjects, $studyGroups, $examHalls, $teachingAssistants)
    {
        if ($subjects->isEmpty() || $studyGroups->isEmpty() || 
            $examHalls->isEmpty() || $teachingAssistants->isEmpty()) {
            return false;
        }

        // Validate hall capacities
        foreach ($studyGroups as $group) {
            $totalStudents = $group->subgroups->sum('capacity');
            $suitableHalls = $examHalls->filter(function($hall) use ($totalStudents) {
                return $hall->capacity >= $totalStudents;
            });

            if ($suitableHalls->isEmpty()) {
                $this->logSchedulingError(
                    $group->group_id,
                    null,
                    "No suitable hall found for group {$group->group_name} with {$totalStudents} students"
                );
                return false;
            }
        }

        // Validate minimum TAs required
        $totalExams = $subjects->count() * $studyGroups->count();
        $availableTAs = $teachingAssistants->where('status', 'basic')->count();
        
        if ($availableTAs < ceil($totalExams / 2)) {
            $this->logSchedulingError(
                null,
                null,
                "Insufficient teaching assistants. Required: " . ceil($totalExams / 2) . ", Available: {$availableTAs}"
            );
            return false;
        }

        return true;
    }

    private function partitionStudyGroups($studyGroups, $examHalls)
    {
        $partitions = [];
        $totalHalls = $examHalls->count();
        
        if ($totalHalls === 0) {
            throw new \Exception('No exam halls available for partitioning');
        }

        // Group study groups by their subgroups
        $subgroupGroups = $studyGroups->groupBy('subgroup_id');
        
        // Calculate total students across all groups
        $totalStudents = $studyGroups->sum(function($group) {
            return $group->subgroups->sum('capacity');
        });
        
        // Calculate average students per hall
        $avgStudentsPerHall = ceil($totalStudents / $totalHalls);
        
        // Sort halls by capacity in descending order
        $sortedHalls = $examHalls->sortByDesc('capacity')->values();
        
        // Initialize partition tracking
        $currentHallIndex = 0;
        $currentPartition = [
            'groups' => [],
            'total_students' => 0,
            'hall' => $sortedHalls[0]
        ];
        
        // Process each subgroup's groups
        foreach ($subgroupGroups as $subgroupGroups) {
            // Sort groups within subgroup by student count
            $sortedGroups = $subgroupGroups->sortByDesc(function($group) {
                return $group->subgroups->sum('capacity');
            });
            
            foreach ($sortedGroups as $group) {
                $groupStudentCount = $group->subgroups->sum('capacity');
                
                // Check if adding this group would exceed hall capacity
                if ($currentPartition['total_students'] + $groupStudentCount > $currentPartition['hall']->capacity) {
                    // Save current partition if it has groups
                    if (!empty($currentPartition['groups'])) {
                        $partitions[] = $currentPartition;
                    }
                    
                    // Move to next hall
                    $currentHallIndex = ($currentHallIndex + 1) % $totalHalls;
                    
                    // Initialize new partition
                    $currentPartition = [
                        'groups' => [],
                        'total_students' => 0,
                        'hall' => $sortedHalls[$currentHallIndex]
                    ];
                }
                
                // Add group to current partition
                $currentPartition['groups'][] = $group;
                $currentPartition['total_students'] += $groupStudentCount;
            }
        }
        
        // Add the last partition if it has groups
        if (!empty($currentPartition['groups'])) {
            $partitions[] = $currentPartition;
        }
        
        // Validate partitions
        $this->validatePartitions($partitions, $examHalls);
        
        return $partitions;
    }

    private function validatePartitions($partitions, $examHalls)
    {
        $errors = [];
        
        // Check if all groups are assigned
        $assignedGroupIds = collect($partitions)->pluck('groups')->flatten()->pluck('group_id')->unique();
        $totalGroups = $examHalls->count();
        
        if ($assignedGroupIds->count() !== $totalGroups) {
            $errors[] = "Not all groups were assigned to partitions";
        }
        
        // Check hall capacity constraints
        foreach ($partitions as $index => $partition) {
            if ($partition['total_students'] > $partition['hall']->capacity) {
                $errors[] = "Partition {$index} exceeds hall capacity: {$partition['total_students']} > {$partition['hall']->capacity}";
            }
        }
        
        // Check for balanced distribution
        $avgStudentsPerHall = collect($partitions)->avg('total_students');
        $maxDeviation = $avgStudentsPerHall * 0.2; // Allow 20% deviation
        
        foreach ($partitions as $index => $partition) {
            $deviation = abs($partition['total_students'] - $avgStudentsPerHall);
            if ($deviation > $maxDeviation) {
                $errors[] = "Partition {$index} has unbalanced student distribution: {$partition['total_students']} students";
            }
        }
        
        // Log any validation errors
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Log::warning("Partition validation error: {$error}");
            }
            
            // If there are critical errors, throw an exception
            if (in_array("Not all groups were assigned to partitions", $errors)) {
                throw new \Exception("Failed to create valid partitions: " . implode(", ", $errors));
            }
        }
    }

    private function optimizePartitionDistribution($partitions, $examHalls)
    {
        $optimized = false;
        $maxIterations = 3;
        $iteration = 0;
        
        while (!$optimized && $iteration < $maxIterations) {
            $optimized = true;
            
            // Calculate average students per hall
            $avgStudentsPerHall = collect($partitions)->avg('total_students');
            
            // Try to balance partitions
            for ($i = 0; $i < count($partitions); $i++) {
                for ($j = $i + 1; $j < count($partitions); $j++) {
                    $partition1 = &$partitions[$i];
                    $partition2 = &$partitions[$j];
                    
                    // Calculate current deviation from average
                    $deviation1 = abs($partition1['total_students'] - $avgStudentsPerHall);
                    $deviation2 = abs($partition2['total_students'] - $avgStudentsPerHall);
                    
                    // If both partitions are significantly off from average
                    if ($deviation1 > $avgStudentsPerHall * 0.1 && $deviation2 > $avgStudentsPerHall * 0.1) {
                        // Try to swap groups between partitions
                        $this->attemptPartitionSwap($partition1, $partition2);
                        
                        // Recalculate deviations
                        $newDeviation1 = abs($partition1['total_students'] - $avgStudentsPerHall);
                        $newDeviation2 = abs($partition2['total_students'] - $avgStudentsPerHall);
                        
                        // If the swap improved the distribution
                        if ($newDeviation1 + $newDeviation2 < $deviation1 + $deviation2) {
                            $optimized = false;
                        }
                    }
                }
            }
            
            $iteration++;
        }
        
        return $partitions;
    }

    private function attemptPartitionSwap(&$partition1, &$partition2)
    {
        // Find the smallest group in the larger partition
        $largerPartition = $partition1['total_students'] > $partition2['total_students'] ? $partition1 : $partition2;
        $smallerPartition = $partition1['total_students'] > $partition2['total_students'] ? $partition2 : $partition1;
        
        // Sort groups by student count
        $largerGroups = collect($largerPartition['groups'])->sortBy(function($group) {
            return $group->subgroups->sum('capacity');
        });
        
        $smallerGroups = collect($smallerPartition['groups'])->sortByDesc(function($group) {
            return $group->subgroups->sum('capacity');
        });
        
        // Try to find a beneficial swap
        foreach ($largerGroups as $largerGroup) {
            $largerGroupStudents = $largerGroup->subgroups->sum('capacity');
            
            foreach ($smallerGroups as $smallerGroup) {
                $smallerGroupStudents = $smallerGroup->subgroups->sum('capacity');
                
                // Calculate the impact of the swap
                $newLargerTotal = $largerPartition['total_students'] - $largerGroupStudents + $smallerGroupStudents;
                $newSmallerTotal = $smallerPartition['total_students'] - $smallerGroupStudents + $largerGroupStudents;
                
                // Check if the swap would improve the distribution
                if ($newLargerTotal <= $largerPartition['hall']->capacity &&
                    $newSmallerTotal <= $smallerPartition['hall']->capacity) {
                    
                    // Perform the swap
                    $largerPartition['groups'] = array_diff($largerPartition['groups'], [$largerGroup]);
                    $largerPartition['groups'][] = $smallerGroup;
                    $largerPartition['total_students'] = $newLargerTotal;
                    
                    $smallerPartition['groups'] = array_diff($smallerPartition['groups'], [$smallerGroup]);
                    $smallerPartition['groups'][] = $largerGroup;
                    $smallerPartition['total_students'] = $newSmallerTotal;
                    
                    return true;
                }
            }
        }
        
        return false;
    }

    private function generatePartitionSchedule($groups, $hall, $subjects, $teachingAssistants, &$generationStats)
    {
        $schedule = [];
        $availableTAs = $teachingAssistants->toArray();
        
        foreach ($groups as $group) {
            foreach ($subjects as $subject) {
                $scheduleEntry = $this->createScheduleEntry(
                    $group,
                    $subject,
                    $hall,
                    $availableTAs,
                    $generationStats
                );
                
                if ($scheduleEntry) {
                    $schedule[] = $scheduleEntry;
                    $generationStats['total_exams']++;
                } else {
                    $generationStats['errors']++;
                }
            }
        }
        
        return $schedule;
    }

    private function createScheduleEntry($group, $subject, $hall, &$availableTAs, &$generationStats)
    {
        // Calculate exam time
        $examTime = $this->calculateExamTime($group, $subject, $hall);
        
        // Find available TA based on priority
        $assignedTA = $this->findAvailableTA($availableTAs, $group, $subject, $examTime);
        
        if (!$assignedTA) {
            $this->logSchedulingError(
                $group->group_id,
                $subject->subject_id,
                'No available teaching assistant found'
            );
            return null;
        }
        
        $generationStats['assigned_tas']++;
        
        return [
            'exam_day' => $examTime['day'],
            'exam_date' => $examTime['date'],
            'subject_id' => $subject->subject_id,
            'group_id' => $group->group_id,
            'hall_id' => $hall->hall_id,
            'ta_id' => $assignedTA['ta_id'],
            'start_time' => $examTime['start_time'],
            'end_time' => $examTime['end_time'],
            'duration' => $subject->duration,
            'buffer_time' => $examTime['buffer_time']
        ];
    }

    private function findAvailableTA(&$availableTAs, $group, $subject, $examTime)
    {
        // First try to find a Basic Teaching Assistant
        foreach ($availableTAs as $key => $ta) {
            if ($ta['status'] === 'Basic' && 
                $ta['role'] === 'Teaching Assistant' && 
                $this->isTAAvailable($ta, $group, $subject, $examTime)) {
                $assignedTA = $ta;
                unset($availableTAs[$key]);
                return $assignedTA;
            }
        }

        // Then try to find a Basic Doctor
        foreach ($availableTAs as $key => $ta) {
            if ($ta['status'] === 'Basic' && 
                $ta['role'] === 'Doctor' && 
                $this->isTAAvailable($ta, $group, $subject, $examTime)) {
                $assignedTA = $ta;
                unset($availableTAs[$key]);
                return $assignedTA;
            }
        }

        // Finally, try Reserve TAs
        foreach ($availableTAs as $key => $ta) {
            if ($this->isTAAvailable($ta, $group, $subject, $examTime)) {
                $assignedTA = $ta;
                unset($availableTAs[$key]);
                return $assignedTA;
            }
        }

        return null;
    }

    private function isTAAvailable($ta, $group, $subject, $examTime)
    {
        // Validate TA status and role
        if (!in_array($ta['status'], ['Basic', 'Reserve'])) {
            Log::warning("Invalid TA status: {$ta['status']} for TA ID: {$ta['ta_id']}");
            return false;
        }

        if (!in_array($ta['role'], ['Teaching Assistant', 'Doctor'])) {
            Log::warning("Invalid TA role: {$ta['role']} for TA ID: {$ta['ta_id']}");
            return false;
        }

        // Check if TA has day off
        if ($this->hasDayOff($ta, $examTime['day'])) {
            return false;
        }
        
        // Check if TA is already assigned to another exam at the same time
        if ($this->hasTimeConflict($ta, $examTime['day'], $examTime['start_time'], $examTime['end_time'])) {
            return false;
        }

        // Check if subject requires a Doctor and TA is not a Doctor
        if ($subject->requires_doctor && $ta['role'] !== 'Doctor') {
            return false;
        }
        
        return true;
    }

    private function logSchedulingError($groupId, $subjectId, $error)
    {
        Log::warning("Scheduling error for group {$groupId}, subject {$subjectId}: {$error}");
        SchedulingError::create([
            'group_id' => $groupId,
            'subject_id' => $subjectId,
            'error_message' => $error
        ]);
    }

    private function clearExistingData()
    {
        try {
            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Clear all related tables in the correct order
            DB::table('exam_schedule_teaching_assistants')->truncate();
            DB::table('exam_schedule')->truncate();
            DB::table('last_exam_schedule')->truncate();
            DB::table('scheduling_errors')->truncate();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            Log::error('Error clearing existing data: ' . $e->getMessage());
            throw $e;
        }
    }

    private function saveSchedule($schedule)
    {
        foreach ($schedule as $entry) {
            ExamSchedule::create($entry);
        }
        
        // Save to last_exam_schedule
        LastExamSchedule::create([
            'schedule_data' => json_encode($schedule)
        ]);
    }

    public function clearData()
    {
        try {
            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Clear all related tables in the correct order
            DB::table('exam_schedule_teaching_assistants')->truncate();
            DB::table('exam_schedule')->truncate();
            DB::table('scheduling_errors')->truncate();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return response()->json([
                'message' => 'All data cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error clearing data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateSubgroups($studyGroups, $examHalls)
    {
        try {
            // Clear existing subgroups
            Subgroup::clearAll();

            // Sort halls by capacity in descending order
            $sortedHalls = $examHalls->sortByDesc('capacity')->values();
            
            foreach ($studyGroups as $group) {
                $totalStudents = $group->number_of_groups;
                $remainingStudents = $totalStudents;
                $subgroupNumber = 1;

                while ($remainingStudents > 0) {
                    // Find the largest hall that can accommodate the remaining students
                    $suitableHall = $sortedHalls->first(function($hall) use ($remainingStudents) {
                        return $hall->capacity >= $remainingStudents;
                    });

                    if (!$suitableHall) {
                        // If no single hall can accommodate all students, use the largest hall
                        $suitableHall = $sortedHalls->first();
                    }

                    // Calculate subgroup capacity
                    $subgroupCapacity = min($remainingStudents, $suitableHall->capacity);

                    // Create the subgroup
                    Subgroup::create([
                        'name' => "{$group->group_name} Subgroup {$subgroupNumber}",
                        'group_id' => $group->group_id,
                        'capacity' => $subgroupCapacity
                    ]);

                    $remainingStudents -= $subgroupCapacity;
                    $subgroupNumber++;
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error generating subgroups: ' . $e->getMessage());
            throw $e;
        }
    }

    private function validateDayOffs($ta, $examDay)
    {
        // Check if the day off is in the future
        if (Carbon::parse($examDay)->isPast()) {
            return false;
        }
        
        // Check if the day off is within a reasonable range (e.g., next 6 months)
        if (Carbon::parse($examDay)->isAfter(now()->addMonths(6))) {
            return false;
        }
        
        return true;
    }

    private function calculateTAWorkload($ta)
    {
        return ExamSchedule::where('ta_id', $ta->ta_id)
            ->where('exam_day', '>=', now())
            ->count();
    }
}
