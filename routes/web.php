<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
  return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
  Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

  Route::group(['prefix' => 'channels/{channel}'], function () {
    Route::get('/', [ChannelController::class, 'show'])->name('channels.show');
    Route::resource('videos', VideoController::class)->only(['index', 'show']);
  });
});

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('auth/google', [AuthController::class, 'redirect'])
  ->name('google.login');
Route::get('auth/google/callback', [AuthController::class, 'callback']);
