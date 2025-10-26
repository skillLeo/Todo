<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});







 

Route::get('/', [TaskController::class, 'index'])->name('task.index');


// Authentication Routes (Ensure Laravel Breeze/Jetstream is installed)

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/home', '/dashboard');
    
    // Tasks Resource Routes
    Route::resource('tasks', TaskController::class)->except(['show']);
    
    // Task Completion Routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::post('/tasks/{task}/uncomplete', [TaskController::class, 'uncomplete'])->name('tasks.uncomplete');
    
    // Task Progress & Statistics
    Route::get('/tasks/{task}/progress', [TaskController::class, 'progress'])->name('tasks.progress');
    Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');
    
    // Bulk Actions
    Route::post('/tasks/bulk-complete', [TaskController::class, 'bulkComplete'])->name('tasks.bulk-complete');
    Route::post('/tasks/bulk-delete', [TaskController::class, 'bulkDelete'])->name('tasks.bulk-delete');
    
    // Task Notes
    Route::post('/tasks/{task}/completions/{completion}/notes', [TaskController::class, 'addCompletionNote'])->name('tasks.completion-notes');
    
    // Archive Tasks
    Route::post('/tasks/{task}/archive', [TaskController::class, 'archive'])->name('tasks.archive');
    Route::get('/tasks-archived', [TaskController::class, 'archived'])->name('tasks.archived');
    Route::post('/tasks/{task}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
