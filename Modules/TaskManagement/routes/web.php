<?php

use Illuminate\Support\Facades\Route;
use Modules\TaskManagement\Http\Controllers\TaskManagementController;
use Modules\TaskManagement\Http\Controllers\ProjectController;
use Modules\TaskManagement\Http\Controllers\ErrorController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Project Dashboard (main page)
    Route::get('/', [ProjectController::class, 'dashboard'])->name('projects.dashboard');
    Route::get('/projects', [ProjectController::class, 'dashboard'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Task routes with project filtering
    Route::get('/projects/{project}/tasks', [TaskManagementController::class, 'index'])->name('taskmanagement.index');
    Route::get('/projects/{project}/tasks/create', [TaskManagementController::class, 'create'])->name('taskmanagement.create');
    Route::post('/projects/{project}/tasks', [TaskManagementController::class, 'store'])->name('taskmanagement.store');
    Route::get('/projects/{project}/tasks/{task}', [TaskManagementController::class, 'show'])->name('taskmanagement.show');
    Route::get('/projects/{project}/tasks/{task}/edit', [TaskManagementController::class, 'edit'])->name('taskmanagement.edit');
    Route::put('/projects/{project}/tasks/{task}/update', [TaskManagementController::class, 'update'])->name('taskmanagement.update');
    Route::delete('/projects/{project}/tasks/{task}', [TaskManagementController::class, 'destroy'])->name('taskmanagement.destroy');
    Route::post('/projects/{project}/tasks/reorder', [TaskManagementController::class, 'reorder'])->name('taskmanagement.reorder');

    // API routes for AJAX calls
    Route::prefix('api')->group(function () {
        Route::apiResource('projects', ProjectController::class)->names('api.projects');
    });

    // Test error pages (remove in production)
    Route::prefix('test-errors')->group(function () {
        Route::get('/404', [ErrorController::class, 'test404'])->name('test.404');
        Route::get('/500', [ErrorController::class, 'test500'])->name('test.500');
        Route::get('/403', [ErrorController::class, 'test403'])->name('test.403');
        Route::get('/503', [ErrorController::class, 'test503'])->name('test.503');
    });
});

// Catch-all route for 404 errors (must be outside auth middleware)
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
