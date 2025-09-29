<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/projects');
    }
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return redirect('/projects');
})->middleware(['auth', 'verified'])->name('dashboard');

// Comment out the original auth routes since we're using module auth
// require __DIR__.'/auth.php';
