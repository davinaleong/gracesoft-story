<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Repository;
use App\Models\Subscription;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class WorkspaceController extends Controller
{
    public function labels(Request $request): View
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $repositories = $this->repositories($user->id);

        $labels = $user->labels()
            ->withCount('commits')
            ->orderBy('name')
            ->get();

        return view('workspace.labels', [
            'repositories' => $repositories,
            'labels' => $labels,
            'isLoading' => (bool) $request->boolean('loading'),
        ]);
    }

    public function insights(Request $request): View
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $repositories = $this->repositories($user->id);
        $repoIds = $repositories->pluck('id');

        $totalChapters = 0;
        $topLabel = null;
        $weeklyChapters = 0;
        $labelChart = collect();
        $activityChart = collect();

        if ($repoIds->isNotEmpty()) {
            $totalChapters = Repository::query()
                ->whereIn('id', $repoIds)
                ->withCount('commits')
                ->get()
                ->sum('commits_count');

            $weeklyChapters = Repository::query()
                ->whereIn('id', $repoIds)
                ->with(['commits' => static function ($query): void {
                    $query->where('committed_at', '>=', now()->subDays(7));
                }])
                ->get()
                ->sum(static fn (Repository $repo): int => $repo->commits->count());

            $labelRows = $user->labels()
                ->whereHas('commits', static function ($query) use ($repoIds): void {
                    $query->whereIn('repository_id', $repoIds);
                })
                ->withCount(['commits as repo_commits_count' => static function ($query) use ($repoIds): void {
                    $query->whereIn('repository_id', $repoIds);
                }])
                ->orderByDesc('repo_commits_count')
                ->get();

            $topLabel = $labelRows->first();
            $labelChart = $labelRows->take(5);

            $activityChart = collect(range(6, 0))->map(function (int $daysAgo) use ($repoIds): array {
                $date = now()->subDays($daysAgo);

                $count = Repository::query()
                    ->whereIn('id', $repoIds)
                    ->with(['commits' => static function ($query) use ($date): void {
                        $query
                            ->whereDate('committed_at', $date->toDateString());
                    }])
                    ->get()
                    ->sum(static fn (Repository $repo): int => $repo->commits->count());

                return [
                    'label' => $date->format('D'),
                    'value' => $count,
                ];
            });
        }

        $activePlan = $this->activePlan($user->id);
        $insightsEnabled = (bool) ($activePlan?->can_use_insights ?? false);

        return view('workspace.insights', [
            'repositories' => $repositories,
            'insightsEnabled' => $insightsEnabled,
            'activePlan' => $activePlan,
            'totalChapters' => $totalChapters,
            'topLabel' => $topLabel,
            'weeklyChapters' => $weeklyChapters,
            'labelChart' => $labelChart,
            'activityChart' => $activityChart,
            'isLoading' => (bool) $request->boolean('loading'),
        ]);
    }

    public function settings(Request $request): View
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $repositories = $this->repositories($user->id);

        $githubAccount = $user->gitAccounts()
            ->where('provider', 'github')
            ->first();

        $activePlan = $this->activePlan($user->id);

        return view('workspace.settings', [
            'repositories' => $repositories,
            'githubAccount' => $githubAccount,
            'activePlan' => $activePlan,
            'isLoading' => (bool) $request->boolean('loading'),
        ]);
    }

    private function repositories(int $userId): Collection
    {
        return Repository::query()
            ->where('user_id', $userId)
            ->orderBy('full_name')
            ->get(['id', 'name', 'full_name', 'last_synced_at']);
    }

    private function activePlan(int $userId): ?Plan
    {
        $subscription = Subscription::query()
            ->whereIn('account_id', function ($query) use ($userId): void {
                $query
                    ->select('id')
                    ->from('accounts')
                    ->where('owner_user_id', $userId);
            })
            ->where('status', 'active')
            ->latest('updated_at')
            ->first();

        if ($subscription === null) {
            return Plan::query()->where('slug', 'free')->first();
        }

        return Plan::query()->find($subscription->plan_id);
    }
}
