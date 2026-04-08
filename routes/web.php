<?php

use App\Http\Controllers\GitHubOAuthController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\NotificationCenterController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\CommitLabelController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules;

Route::middleware('auth')->group(function (): void {
    Route::get('/', function (Request $request) {
        $user = $request->user();

        $repositories = $user?->repositories()
            ->orderBy('full_name')
            ->get(['id', 'name', 'full_name'])
            ?? collect();

        $hasGitHubAccount = $user?->gitAccounts()
            ->where('provider', 'github')
            ->exists() ?? false;

        return view('welcome', [
            'repositories' => $repositories,
            'hasGitHubAccount' => $hasGitHubAccount,
        ]);
    });

    Route::get('/auth/github/redirect', [GitHubOAuthController::class, 'redirect'])
        ->name('auth.github.redirect');

    Route::get('/auth/github/callback', [GitHubOAuthController::class, 'callback'])
        ->name('auth.github.callback');

    Route::delete('/auth/github/disconnect', [GitHubOAuthController::class, 'disconnect'])
        ->name('auth.github.disconnect');

    Route::post('/sync/github/refresh', [SyncController::class, 'refreshGitHub'])
        ->name('sync.github.refresh');

    Route::get('/story/{repo}', [StoryController::class, 'timeline'])
        ->name('story.timeline');

    Route::get('/story/{repo}/chapter/{commit}', [StoryController::class, 'chapter'])
        ->name('story.chapter');

    Route::post('/labels', [LabelController::class, 'store'])
        ->name('labels.store');

    Route::get('/labels/manage', [WorkspaceController::class, 'labels'])
        ->name('labels.manage');

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

    Route::get('/notifications', [NotificationCenterController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/{id}/read', [NotificationCenterController::class, 'markRead'])
        ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationCenterController::class, 'markAllRead'])
        ->name('notifications.read-all');

    Route::get('/insights', [WorkspaceController::class, 'insights'])
        ->name('insights.index');

    Route::get('/settings', [WorkspaceController::class, 'settings'])
        ->name('settings.index');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', function (Request $request): RedirectResponse {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = \App\Models\User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);

        // Send verification after the response so registration feels instant.
        dispatch(function () use ($user): void {
            $user->sendEmailVerificationNotification();
        })->afterResponse();

        return redirect()->route('verification.notice');
    });

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Request $request): RedirectResponse {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    });

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request): RedirectResponse {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('/reset-password/{token}', function (Request $request, string $token) {
        return view('auth.reset-password', ['request' => $request, 'token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request): RedirectResponse {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => \Illuminate\Support\Str::random(60),
                ])->save();
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    })->name('password.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/verify-email', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request): RedirectResponse {
        $request->fulfill();

        return redirect('/')->with('status', 'Email verified successfully.');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request): RedirectResponse {
        $request->user()?->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent.');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::get('/confirm-password', function () {
        return view('auth.confirm-password');
    })->name('password.confirm');

    Route::post('/confirm-password', function (Request $request): RedirectResponse {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! $user || ! Hash::check($request->string('password')->toString(), $user->password)) {
            return back()->withErrors(['password' => 'The password is incorrect.']);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended('/');
    });

    Route::post('/logout', function (Request $request): RedirectResponse {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});
