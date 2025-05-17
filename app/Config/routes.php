<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/**
 * Web Routes
 */
Route::get('/', [\App\Controllers\HomeController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [\App\Controllers\AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [\App\Controllers\AuthController::class, 'login']);
Route::get('/register', [\App\Controllers\AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [\App\Controllers\AuthController::class, 'register']);
Route::post('/logout', [\App\Controllers\AuthController::class, 'logout'])->name('logout');

// Items routes
Route::get('/items', [\App\Controllers\ItemController::class, 'index'])->name('items.index');
Route::get('/items/create', [\App\Controllers\ItemController::class, 'create'])->name('items.create');
Route::post('/items', [\App\Controllers\ItemController::class, 'store'])->name('items.store');
Route::get('/items/{id}', [\App\Controllers\ItemController::class, 'show'])->name('items.show');
Route::get('/items/{id}/edit', [\App\Controllers\ItemController::class, 'edit'])->name('items.edit');
Route::put('/items/{id}', [\App\Controllers\ItemController::class, 'update'])->name('items.update');
Route::delete('/items/{id}', [\App\Controllers\ItemController::class, 'destroy'])->name('items.destroy');

// Matches routes
Route::post('/matches', [\App\Controllers\MatchController::class, 'store'])->name('matches.store');
Route::put('/matches/{id}', [\App\Controllers\MatchController::class, 'update'])->name('matches.update');

// Admin routes
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/', [\App\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [\App\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/items', [\App\Controllers\AdminController::class, 'items'])->name('admin.items');
    Route::get('/matches', [\App\Controllers\AdminController::class, 'matches'])->name('admin.matches');
});

// API routes
Route::prefix('api')->group(function () {
    Route::get('/items/search', [\App\Controllers\Api\ItemController::class, 'search'])->name('api.items.search');
    Route::get('/items/{id}', [\App\Controllers\Api\ItemController::class, 'show'])->name('api.items.show');
});
