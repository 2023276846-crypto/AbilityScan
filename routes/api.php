<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PWDController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MessageController;

// Public routes
Route::post('/register/pwd', [AuthController::class, 'registerPWD']);
Route::post('/register/employer', [AuthController::class, 'registerEmployer']);
Route::post('/login', [AuthController::class, 'login']);

// Public portfolio routes
Route::get('/portfolio/search', [PWDController::class, 'searchPortfolio']);
Route::get('/portfolio/{id}', [PWDController::class, 'publicPortfolio']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // PWD Profile
    Route::get('/pwd/profile', [PWDController::class, 'getProfile']);
    Route::post('/pwd/profile', [PWDController::class, 'saveProfile']);

    // PWD QR Code
    Route::get('/pwd/qr', [PWDController::class, 'getQR']);
    Route::post('/pwd/qr/generate', [PWDController::class, 'generateQR']);

    // PWD Jobs
    Route::get('/pwd/jobs', [PWDController::class, 'getJobs']);
    Route::post('/pwd/apply', [PWDController::class, 'applyJob']);

    // PWD Mailbox
    Route::get('/pwd/messages', [PWDController::class, 'getMessages']);
    Route::get('/pwd/applications', [PWDController::class, 'getApplications']);
    // Employer routes
    Route::get('/employer/dashboard', [EmployerController::class, 'dashboard']);
    // Employer Jobs
    Route::post('/employer/jobs', [EmployerController::class, 'postJob']);
    Route::get('/employer/jobs', [EmployerController::class, 'getJobs']);
    Route::put('/employer/jobs/{id}', [EmployerController::class, 'updateJob']);
    Route::delete('/employer/jobs/{id}', [EmployerController::class, 'deleteJob']);
    Route::get('/employer/jobs/{id}', [EmployerController::class, 'getJob']);
    // Employer Profile
    Route::get('/employer/profile', [EmployerController::class, 'getProfile']);
    Route::post('/employer/profile', [EmployerController::class, 'saveProfile']);
    Route::get('/employer/candidates', [EmployerController::class, 'getCandidates']);
    // Employer Applications
    Route::get('/employer/applications', [EmployerController::class, 'getApplications']);
    Route::put('/employer/applications/{id}', [EmployerController::class, 'updateApplicationStatus']);
    Route::get('/pwd/dashboard', [PWDController::class, 'getDashboard']);
    // Admin routes
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/pwds', [AdminController::class, 'getPWDs']);
    Route::put('/admin/pwds/{id}/status', [AdminController::class, 'updatePWDStatus']);
    Route::delete('/admin/pwds/{id}', [AdminController::class, 'deletePWD']);
    Route::get('/admin/employers', [AdminController::class, 'getEmployers']);
    Route::put('/admin/employers/{id}/status', [AdminController::class, 'updateEmployerStatus']);
    Route::delete('/admin/employers/{id}', [AdminController::class, 'deleteEmployer']);
    Route::get('/admin/reports', [AdminController::class, 'getReports']);
    Route::get('/admin/monitor', [AdminController::class, 'getMonitor']);
    Route::get('/admin/algorithm-weights', [AdminController::class, 'getAlgorithmWeights']);
    Route::post('/admin/algorithm-weights', [AdminController::class, 'updateAlgorithmWeights']);
    Route::post('/admin/algorithm-recalculate', [AdminController::class, 'recalculateMatches']);
    Route::get('/pwd/jobs/{id}', [PWDController::class, 'getJob']);
    // Messaging
    Route::get('/messages', [MessageController::class, 'getMessages']);
    Route::post('/messages', [MessageController::class, 'sendMessage']);
    Route::put('/messages/{id}/read', [MessageController::class, 'markAsRead']);
    Route::delete('/messages/{id}', [MessageController::class, 'deleteMessage']);
    Route::get('/messages/conversation/{userId}', [MessageController::class, 'getConversation']);
});