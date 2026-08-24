<?php

use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->prefix('auth')->name('auth.')->group(function () {
    Route::get('/google', [SocialAuthController::class, 'redirect'])->name('google');
    Route::get('/google/callback', [SocialAuthController::class, 'callback'])->name('google.callback');
});
