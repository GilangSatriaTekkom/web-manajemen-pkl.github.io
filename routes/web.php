<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::post('/assign-project', [DashboardController::class, 'assignProject'])->middleware('auth')->name('assign-project');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project.show');


Route::middleware(['auth'])->get('/profile/{id}', [ProfileController::class, 'index'])->name('profile');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/search-users', [SearchController::class, 'searchParticipants']);


Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

Route::get('/project/{projectId}/tasks/{taskId}/data', [TaskController::class, 'getTaskData'])->name('task.data');

Route::post('/tasks/{currentTaskId}/comments', [TaskController::class, 'addComment'])->name('task.addComment');

Route::get('/report', [ReportController::class, 'index'])->name('report.index');
Route::get('/report/filter', [ReportController::class, 'filter'])->name('report.filter');

Route::post('/tasks/update', [TaskController::class, 'updateBoard'])->name('tasks.update');
Route::post('/profile/{id}/profileUpload', [ProfileController::class, 'profileUpload'])->name('profile.profileUpload');

Route::delete('/projects/{project}/participants/{participant}', [ProjectController::class, 'removeParticipant'])->name('projects.removeParticipant');
Route::get('/projects/{project}/add-participant', [ProjectController::class, 'addParticipant'])->name('projects.addParticipant');
Route::get('dashboard/{project}/participants', [DashboardController::class, 'showParticipants'])->name('showParticipants');
Route::get('dashboard/{project}/participantsAdd/', [DashboardController::class, 'storeParticipants'])->name('storeParticipants');

Route::put('/projects/update', [ProjectController::class, 'update'])->name('project.update');
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('project.delete');

