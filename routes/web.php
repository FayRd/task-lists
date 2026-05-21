<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ListShareController;

Route::view('/', 'welcome')->name('home');

// Google Routes
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])
    ->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
    ->name('auth.google.callback');

// Only Authenticated Users
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');
    Route::livewire('/lists', 'pages::todo-lists.index')
        ->name('lists.index');
    Route::livewire('/lists/{todoList}', 'pages::todo-lists.show')
        ->name('lists.show');
    Route::post('/lists/{todoList}/share', [ListShareController::class, 'generate'])
        ->name('lists.share');
    Route::get('/share/{token}/claim', [ListShareController::class, 'claim'])
        ->name('share.claim');
});

// Shared Link Routes
Route::get('/share/{token}', [ListShareController::class, 'preview'])
    ->name('share.preview');
Route::post('share/{token}/import', [ListShareController::class, 'import'])
    ->name('share.import')
    ->middleware(['auth', 'verified']);

require __DIR__.'/settings.php';
