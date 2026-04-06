<?php

namespace App\Http\Controllers;

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
            ->orderByDesc('committed_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('story.timeline', [
            'repository' => $repo,
            'commits' => $commits,
        ]);
    }
}
