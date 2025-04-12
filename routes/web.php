<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
  return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
  Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
  Route::get('channels/{channel}', [ChannelController::class, 'show'])->name('channels.show');

  Route::get('videos/{video}', [VideoController::class, 'show'])->name('videos.show');
  Route::post('videos/{video}', [VideoController::class, 'refresh'])->name('videos.refresh');
  Route::post('videos/{video}/purge-spam', [VideoController::class, 'purgeSpam'])->name('videos.purge-spam');

  Route::delete('comments/{comment}', [CommentController::class, 'destroy'])
    ->name('comments.destroy');
});

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('auth/google', [AuthController::class, 'redirect'])
  ->name('google.login');
Route::get('auth/google/callback', [AuthController::class, 'callback']);
