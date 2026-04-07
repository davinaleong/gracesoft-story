<?php

namespace App\Http\Controllers;

use App\Models\Commit;
use App\Models\Repository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function timeline(Request $request, Repository $repo): View
    {
        $user = $request->user();

        abort_if($user === null, 401);
        abort_if($repo->user_id !== $user->id, 404);

        $commits = $repo->commits()
            ->with('labels')
            ->orderByDesc('committed_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $repositories = $user->repositories()
            ->orderBy('full_name')
            ->get(['id', 'name', 'full_name']);

        return view('story.timeline', [
            'repository' => $repo,
            'commits' => $commits,
            'repositories' => $repositories,
        ]);
    }

    public function chapter(Request $request, Repository $repo, Commit $commit): View
    {
        $user = $request->user();

        abort_if($user === null, 401);
        abort_if($repo->user_id !== $user->id, 404);
        abort_if($commit->repository_id !== $repo->id, 404);

        $commits = $repo->commits()
            ->with('labels')
            ->orderByDesc('committed_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $repositories = $user->repositories()
            ->orderBy('full_name')
            ->get(['id', 'name', 'full_name']);

        $commit->load('labels');

        return view('story.chapter', [
            'repository' => $repo,
            'commit' => $commit,
            'commits' => $commits,
            'repositories' => $repositories,
        ]);
    }
}
