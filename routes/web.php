<?php

use App\Http\Controllers\GitHubOAuthController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\CommitLabelController;
use App\Http\Controllers\SyncController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/github/redirect', [GitHubOAuthController::class, 'redirect'])
    ->name('auth.github.redirect');

Route::get('/auth/github/callback', [GitHubOAuthController::class, 'callback'])
    ->name('auth.github.callback');

Route::post('/sync/github/refresh', [SyncController::class, 'refreshGitHub'])
    ->name('sync.github.refresh');

Route::get('/story/{repo}', [StoryController::class, 'timeline'])
    ->name('story.timeline');

Route::get('/story/{repo}/chapter/{commit}', [StoryController::class, 'chapter'])
    ->name('story.chapter');

Route::post('/labels', [LabelController::class, 'store'])
    ->name('labels.store');

Route::patch('/labels/{label}', [LabelController::class, 'update'])
    ->name('labels.update');

Route::delete('/labels/{label}', [LabelController::class, 'destroy'])
    ->name('labels.destroy');

Route::post('/story/{repo}/commits/{commit}/labels', [CommitLabelController::class, 'attach'])
    ->name('story.commits.labels.attach');

Route::delete('/story/{repo}/commits/{commit}/labels/{label}', [CommitLabelController::class, 'detach'])
    ->name('story.commits.labels.detach');

Route::post('/story/{repo}/labels/bulk-apply', [CommitLabelController::class, 'bulkApply'])
    ->name('story.labels.bulk-apply');
