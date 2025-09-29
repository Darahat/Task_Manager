<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\LoginController;
use Modules\Auth\Http\Controllers\RegisterController;
use Modules\Auth\Http\Controllers\ForgotPasswordController;

// Guest routes (for non-authenticated users)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('auth.login');
    Route::post('login', [LoginController::class, 'store'])->name('auth.login.store');
    
    Route::get('register', [RegisterController::class, 'create'])->name('auth.register');
    Route::post('register', [RegisterController::class, 'store'])->name('auth.register.store');
    
    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('auth.password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('auth.password.email');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('auth.logout');
});

// Resource routes for authenticated users
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('auths', AuthController::class)->names('auth');
});
