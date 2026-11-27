<?php

use App\Http\Controllers\Admin\BoardController as AdminBoardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ThreadController as AdminThreadController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\Userzone\DashboardController;
use App\Http\Controllers\Userzone\PostController as UserzonePostController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\Userzone\ThreadController as UserzoneThreadController;
use Illuminate\Support\Facades\Route;

/*
 * Public Website routes
 */

Route::get('/', [BoardController::class, 'index'])->name('boards.index');
Route::resource('boards', BoardController::class)->only(['show']);
Route::resource('threads', ThreadController::class)->only(['show']);

/*
 * Authentication routes
 */
require __DIR__.'/auth.php';

/*
 * Userzone routes
 */
Route::middleware('auth')->group(function () {
    // For the user's dashboard (after login), only with a verified email address
    Route::get('/dashboard', DashboardController::class)->middleware('verified')->name('dashboard');

    // verified: posting needs a verified email address
    // throttle: at most 10 requests per minute for each logged-in user
    Route::prefix('user')->name('userzone.')->middleware(['verified', 'throttle:10,1'])->group(function () {
        Route::resource('boards.threads', UserzoneThreadController::class)->only(['create', 'store']);
        Route::resource('threads', UserzoneThreadController::class)->only(['destroy']);
        Route::resource('posts', UserzonePostController::class)->only(['store', 'destroy']);
    });

    // For the user's profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
 * Admin routes
 */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('boards', AdminBoardController::class)->except(['show']);
    Route::resource('threads', AdminThreadController::class)->only(['edit', 'update']);
    Route::resource('posts', AdminPostController::class)->only(['edit', 'update']);
});
