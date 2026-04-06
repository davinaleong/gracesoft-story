<?php

use App\Http\Controllers\GitHubOAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/github/redirect', [GitHubOAuthController::class, 'redirect'])
    ->name('auth.github.redirect');

Route::get('/auth/github/callback', [GitHubOAuthController::class, 'callback'])
    ->name('auth.github.callback');
