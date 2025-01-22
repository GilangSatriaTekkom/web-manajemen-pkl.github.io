<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DescriptionController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\Pesertacontroller;

// Redirect to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Auth::routes();

// Routes that require authentication
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/assign-project', [DashboardController::class, 'assignProject'])->name('assign-project');
    Route::get('dashboard/{project}/participants', [DashboardController::class, 'showParticipants'])->name('showParticipants');
    Route::get('dashboard/{project}/participantsAdd/', [DashboardController::class, 'storeParticipants'])->name('storeParticipants');
    Route::get('/search-projects', [DashboardController::class, 'searchProjects'])->name('search.projects');

    // Projects
    Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project.show');
    Route::put('/projects/update', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::delete('/projects/{project}/participants/{participant}', [ProjectController::class, 'removeParticipant'])->name('projects.removeParticipant');
    Route::get('/projects/{project}/add-participant', [ProjectController::class, 'addParticipant'])->name('projects.addParticipant');
    Route::get('/projects/{id}/participants', [ProjectController::class, 'showParticipants'])->name('project.participants');
    Route::post('/projects/{id}/participants', [ProjectController::class, 'addParticipant'])->name('project.addParticipant');
    Route::delete('/project/{id}/remove-participant/{userId}', [ProjectController::class, 'removeParticipant'])->name('project.removeParticipant');

    // Tasks
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::post('/tasks/{taskId}/edit', [TaskController::class, 'updateTask'])->name('tasks.edit');
    Route::post('/tasks/update', [TaskController::class, 'updateBoard'])->name('tasks.update');
    Route::get('/project/{projectId}/tasks/{taskId}/data', [TaskController::class, 'getTaskData'])->name('task.data');
    Route::get('/project/{projectId}/tasks/{taskId}/EditData', [TaskController::class, 'editData']);
    Route::post('/tasks/{currentTaskId}/comments', [TaskController::class, 'addComment'])->name('task.addComment');
    Route::post('/upload-image', [TaskController::class, 'uploadImage']);

    // Profile
    Route::get('/profile/{id}', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/{id}/profileUpload', [ProfileController::class, 'profileUpload'])->name('profile.profileUpload');

    // Reports
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/filter', [ReportController::class, 'filter'])->name('report.filter');

    // Search
    Route::get('/search-users', [SearchController::class, 'searchParticipants']);

    // Pembimbing & Peserta
    Route::get('/pembimbing', [PembimbingController::class, 'get'])->name('pembimbing');
    Route::get('/pembimbing/live-search', [PembimbingController::class, 'liveSearch'])->name('pembimbing.live-search');
    Route::get('/peserta', [Pesertacontroller::class, 'get'])->name('peserta');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
