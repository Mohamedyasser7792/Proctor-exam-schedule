<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeachingAssistantController;
use App\Http\Controllers\StudyGroupController;
use App\Http\Controllers\ExamHallController;
use App\Http\Controllers\StudySubjectController;
use App\Http\Controllers\ExamScheduleController;
use App\Http\Controllers\GenerateController;
use App\Http\Controllers\LatestExamScheduleController;
use App\Http\Controllers\SchedulingErrorController;
use App\Http\Controllers\CleanupController;
use App\Http\Controllers\ExamScheduleTeachingAssistantController;




Route::post('/login', [AuthController::class, 'login']);


Route::prefix('teaching-assistants')->group(function () {
    Route::get('/', [TeachingAssistantController::class, 'index']);
    Route::post('/', [TeachingAssistantController::class, 'store']);
    Route::put('/{id}', [TeachingAssistantController::class, 'update']);
    Route::delete('/delete-all', [TeachingAssistantController::class, 'destroyAll']); // Place this BEFORE the delete by ID route
    Route::delete('/{id}', [TeachingAssistantController::class, 'destroy']);
});

Route::prefix('study-groups')->group(function () {
    Route::get('/', [StudyGroupController::class, 'index']);
    Route::post('/', [StudyGroupController::class, 'store']);
    Route::put('/{id}', [StudyGroupController::class, 'update']);
    Route::delete('/{id}', [StudyGroupController::class, 'destroy']);
});

Route::prefix('exam-halls')->group(function () {
    Route::get('/', [ExamHallController::class, 'index']);
    Route::post('/', [ExamHallController::class, 'store']);
    Route::put('/{id}', [ExamHallController::class, 'update']);
    Route::delete('/{id}', [ExamHallController::class, 'destroy']);
    Route::delete('/', [ExamHallController::class, 'destroyAll']);
});

Route::prefix('study-subjects')->group(function () {
    Route::get('/', [StudySubjectController::class, 'index']);
    Route::post('/', [StudySubjectController::class, 'store']);
    Route::put('/{id}', [StudySubjectController::class, 'update']);
    Route::delete('/{id}', [StudySubjectController::class, 'destroy']);
    Route::delete('/', [StudySubjectController::class, 'destroyAll']);
});

Route::prefix('exam-schedules')->group(function () {
    Route::get('/', [ExamScheduleController::class, 'index']);
    Route::post('/', [ExamScheduleController::class, 'store']);
    Route::get('/{id}', [ExamScheduleController::class, 'show']);
    Route::put('/{id}', [ExamScheduleController::class, 'update']);
    Route::delete('/{id}', [ExamScheduleController::class, 'destroy']);
    Route::delete('/', [ExamScheduleController::class, 'destroyAll']);
});


// New endpoint for the latest exam schedules
Route::post('/generate-exam-schedule', [GenerateController::class, 'generate']);


// New endpoint for the latest exam schedules
Route::get('/latest-exam-schedules', [LatestExamScheduleController::class, 'index']);

// Endpoint to get all scheduling errors
Route::get('/scheduling-errors', [SchedulingErrorController::class, 'index']);

// Endpoint to clear specific data
Route::delete('/clear-data', [CleanupController::class, 'clearData']);


// New endpoint for listing all exam_schedule_teaching_assistants
Route::get('/exam-schedule-teaching-assistants', [ExamScheduleTeachingAssistantController::class, 'index']);





Route::get('/export-last-exam-schedule', [ExamScheduleController::class, 'export']);

Route::get('/teaching-assistant/{ta_id}', [TeachingAssistantController::class, 'getTeachingAssistantDetails']);

Route::get('/export-teaching-assistant/{ta_id}', [TeachingAssistantController::class, 'exportTeachingAssistantDetails']);

// Study Groups Routes
Route::get('/study-groups', [StudyGroupController::class, 'index']);
Route::post('/study-groups', [StudyGroupController::class, 'store']);
Route::put('/study-groups/{id}', [StudyGroupController::class, 'update']);
Route::delete('/study-groups/{id}', [StudyGroupController::class, 'destroy']);
Route::delete('/study-groups', [StudyGroupController::class, 'destroyAll']);



//   php artisan serve