<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CardController;


Route::get('/', function () {
    // return view('welcome');
    return view('auth.login');
});

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::post('/assign-project', [DashboardController::class, 'assignProject'])->middleware('auth')->name('assign-project');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project.show');

Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/search-users', [SearchController::class, 'searchParticipants']);

Route::middleware(['auth'])->get('/profile', [UserController::class, 'index'])->name('profile');

Route::post('/add-card', [CardController::class, 'store'])->name('add.card');
