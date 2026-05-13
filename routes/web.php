<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación
Route::get('/auth/github', [SocialiteController::class, 'redirectToGithub'])->name('auth.github');
Route::get('/auth/github/callback', [SocialiteController::class, 'handleGithubCallback']);
Route::post('/logout', [SocialiteController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});