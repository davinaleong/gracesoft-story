<?php

namespace App\Http\Controllers;

use App\Models\Commit;
use App\Models\Label;
use App\Models\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Throwable;

class StoryController extends Controller
{
    public function timeline(Request $request, Repository $repo): View|JsonResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);
        abort_if($repo->user_id !== $user->id, 404);

        $activeFilters = $this->timelineFilters($request);

        $commits = $this->buildTimelineQuery($repo, $activeFilters)
            ->paginate(25)
            ->withQueryString();

        $availableAuthors = $repo->commits()
            ->whereNotNull('author_name')
            ->where('author_name', '!=', '')
            ->distinct()
            ->orderBy('author_name')
            ->pluck('author_name');

        $availableLabels = Label::query()
            ->where('user_id', $user->id)
            ->whereHas('commits', static function (Builder $query) use ($repo): void {
                $query->where('repository_id', $repo->id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'color']);

        $repositories = $user->repositories()
            ->orderBy('full_name')
            ->get(['id', 'name', 'full_name']);

        $viewData = [
            'repository' => $repo,
            'commits' => $commits,
            'repositories' => $repositories,
            'availableAuthors' => $availableAuthors,
            'availableLabels' => $availableLabels,
            'activeFilters' => $activeFilters,
            'isLoading' => (bool) $request->boolean('loading'),
        ];

        if ($request->boolean('fragment')) {
            $html = view('story.partials.commit-rows', $viewData)->render();

            return response()->json([
                'html' => $html,
                'nextPageUrl' => $commits->nextPageUrl(),
            ]);
        }

        return view('story.timeline', $viewData);
    }

    public function chapter(Request $request, Repository $repo, Commit $commit): View
    {
        $user = $request->user();

        abort_if($user === null, 401);
        abort_if($repo->user_id !== $user->id, 404);
        abort_if($commit->repository_id !== $repo->id, 404);

        $activeFilters = $this->timelineFilters($request);

        $commits = $this->buildTimelineQuery($repo, $activeFilters)
            ->paginate(25)
            ->withQueryString();

        $repositories = $user->repositories()
            ->orderBy('full_name')
            ->get(['id', 'name', 'full_name']);

        $availableLabels = Label::query()
            ->where('user_id', $user->id)
            ->whereHas('commits', static function (Builder $query) use ($repo): void {
                $query->where('repository_id', $repo->id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'color']);

        $commit->load('labels');

        return view('story.chapter', [
            'repository' => $repo,
            'commit' => $commit,
            'commits' => $commits,
            'repositories' => $repositories,
            'activeFilters' => $activeFilters,
            'availableLabels' => $availableLabels,
        ]);
    }

    /**
     * @return array{author: string, label_id: string, from: string, to: string}
     */
    private function timelineFilters(Request $request): array
    {
        return [
            'author' => trim((string) $request->query('author', '')),
            'label_id' => trim((string) $request->query('label_id', '')),
            'from' => trim((string) $request->query('from', '')),
            'to' => trim((string) $request->query('to', '')),
        ];
    }

    /**
     * @param array{author: string, label_id: string, from: string, to: string} $filters
     */
    private function buildTimelineQuery(Repository $repo, array $filters): HasMany
    {
        return $repo->commits()
            ->with('labels')
            ->when($filters['author'] !== '', function (Builder $query) use ($filters): void {
                $author = $filters['author'];

                $query->where(static function (Builder $inner) use ($author): void {
                    $inner->where('author_name', 'like', '%'.$author.'%')
                        ->orWhere('author_email', 'like', '%'.$author.'%');
                });
            })
            ->when($filters['label_id'] !== '' && ctype_digit($filters['label_id']), function (Builder $query) use ($filters): void {
                $labelId = (int) $filters['label_id'];

                $query->whereHas('labels', static function (Builder $labelQuery) use ($labelId): void {
                    $labelQuery->where('labels.id', $labelId);
                });
            })
            ->when($filters['from'] !== '', function (Builder $query) use ($filters): void {
                $from = $this->safeDate($filters['from'], true);

                if ($from !== null) {
                    $query->where('committed_at', '>=', $from);
                }
            })
            ->when($filters['to'] !== '', function (Builder $query) use ($filters): void {
                $to = $this->safeDate($filters['to'], false);

                if ($to !== null) {
                    $query->where('committed_at', '<=', $to);
                }
            })
            ->orderByDesc('committed_at')
            ->orderByDesc('id');
    }

    private function safeDate(string $value, bool $startOfDay): ?Carbon
    {
        try {
            $date = Carbon::parse($value);

            return $startOfDay ? $date->startOfDay() : $date->endOfDay();
        } catch (Throwable) {
            return null;
        }
    }
}
