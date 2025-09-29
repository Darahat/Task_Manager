<?php

use Illuminate\Support\Facades\Route;
use Modules\TaskManagement\Http\Controllers\TaskManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('taskmanagements', TaskManagementController::class)->names('taskmanagement');
});
