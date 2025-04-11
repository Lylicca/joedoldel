<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
  return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
  Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('auth/google', [AuthController::class, 'redirect'])
  ->name('google.login');
Route::get('auth/google/callback', [AuthController::class, 'callback']);
