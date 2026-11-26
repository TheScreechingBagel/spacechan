<?php

use App\Http\Controllers\ThreadController;
use App\Http\Controllers\Userzone\DashboardController;
use App\Http\Controllers\Userzone\PostController as UserzonePostController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\Userzone\ThreadController as UserzoneThreadController;
use Illuminate\Support\Facades\Route;

/*
 * Public Website routes
 */

Route::get('/', [ThreadController::class, 'index'])->name('threads.index');
Route::resource('threads', ThreadController::class)->only(['show']);

/*
 * Authentication routes
 */
require __DIR__.'/auth.php';

/*
 * Userzone routes
 */
Route::middleware('auth')->group(function () {
    // For the user's dashboard (after login)
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('user')->name('userzone.')->group(function () {
        Route::resource('threads', UserzoneThreadController::class)->only(['create', 'store', 'destroy']);
        Route::resource('posts', UserzonePostController::class)->only(['store']);
    });

    // For the user's profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
