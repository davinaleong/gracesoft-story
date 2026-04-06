<?php

namespace App\Http\Controllers;

use App\Jobs\SyncRepositoriesJob;
use App\Enums\NotificationType;
use App\Models\GitAccount;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GitHubOAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $state = Str::random(40);

        $request->session()->put('github_oauth_state', $state);

        $query = http_build_query([
            'client_id' => (string) config('services.github.client_id'),
            'redirect_uri' => (string) config('services.github.redirect'),
            'scope' => 'repo read:user',
            'state' => $state,
        ]);

        $authorizeUrl = (string) config('services.github.authorize_url', 'https://github.com/login/oauth/authorize');

        return redirect()->away($authorizeUrl.'?'.$query);
    }

    public function callback(Request $request, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $incomingState = (string) $request->query('state', '');
        $expectedState = (string) $request->session()->pull('github_oauth_state', '');

        abort_if($incomingState === '' || $incomingState !== $expectedState, 422, 'Invalid OAuth state.');

        $code = (string) $request->query('code', '');
        abort_if($code === '', 422, 'Missing OAuth code.');

        $tokenResponse = Http::asForm()->acceptJson()->post(
            (string) config('services.github.token_url', 'https://github.com/login/oauth/access_token'),
            [
                'client_id' => (string) config('services.github.client_id'),
                'client_secret' => (string) config('services.github.client_secret'),
                'code' => $code,
                'redirect_uri' => (string) config('services.github.redirect'),
            ],
        );

        abort_if($tokenResponse->failed(), 502, 'Failed to exchange OAuth token.');

        $token = (string) $tokenResponse->json('access_token', '');
        abort_if($token === '', 502, 'Missing OAuth access token.');

        $userResponse = Http::acceptJson()
            ->withToken($token)
            ->get((string) config('services.github.user_url', 'https://api.github.com/user'));

        abort_if($userResponse->failed(), 502, 'Failed to fetch GitHub user.');

        GitAccount::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'provider' => 'github',
            ],
            [
                'access_token' => $token,
                'refresh_token' => Arr::get($tokenResponse->json(), 'refresh_token'),
                'token_expires_at' => $this->resolveExpiry($tokenResponse->json('expires_in')),
            ],
        );

        $notifications->integrationConnected($user, 'github', [
            'provider' => 'github',
            'account' => (string) $userResponse->json('login', ''),
        ]);

        SyncRepositoriesJob::dispatch($user->id, 'auto');

        return redirect('/')
            ->with('status', 'GitHub account connected.');
    }

    public function disconnect(Request $request, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);

        GitAccount::query()
            ->where('user_id', $user->id)
            ->where('provider', 'github')
            ->delete();

        $notifications->integrationDisconnected($user, 'github', [
            'provider' => 'github',
        ]);

        return back()->with('status', 'GitHub account disconnected.');
    }

    private function resolveExpiry(mixed $expiresIn): ?Carbon
    {
        if (! is_numeric($expiresIn)) {
            return null;
        }

        return now()->addSeconds((int) $expiresIn);
    }
}
