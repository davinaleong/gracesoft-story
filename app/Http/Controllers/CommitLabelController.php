<?php

namespace App\Http\Controllers;

use App\Models\Commit;
use App\Models\Label;
use App\Models\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommitLabelController extends Controller
{
    public function attach(Request $request, Repository $repo, Commit $commit): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);
        $this->assertCommitOwnership($user->id, $repo, $commit);

        $validated = $request->validate([
            'label_id' => ['required', 'integer', 'exists:labels,id'],
        ]);

        $label = Label::query()->findOrFail($validated['label_id']);

        abort_if($label->user_id !== $user->id, 404);

        $commit->labels()->syncWithoutDetaching([$label->id]);

        return back()->with('status', 'Label added to chapter.');
    }

    public function detach(Request $request, Repository $repo, Commit $commit, Label $label): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);
        $this->assertCommitOwnership($user->id, $repo, $commit);
        abort_if($label->user_id !== $user->id, 404);

        $commit->labels()->detach($label->id);

        return back()->with('status', 'Label removed from chapter.');
    }

    public function bulkApply(Request $request, Repository $repo): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);
        abort_if($repo->user_id !== $user->id, 404);

        $validated = $request->validate([
            'label_id' => ['required', 'integer', 'exists:labels,id'],
            'commit_ids' => ['required', 'array', 'min:1'],
            'commit_ids.*' => ['integer', 'distinct'],
        ]);

        $label = Label::query()->findOrFail($validated['label_id']);
        abort_if($label->user_id !== $user->id, 404);

        $commits = $repo->commits()
            ->whereIn('id', $validated['commit_ids'])
            ->get();

        foreach ($commits as $commit) {
            $commit->labels()->syncWithoutDetaching([$label->id]);
        }

        return back()->with('status', 'Label applied to selected chapters.');
    }

    private function assertCommitOwnership(int $userId, Repository $repo, Commit $commit): void
    {
        abort_if($repo->user_id !== $userId, 404);
        abort_if($commit->repository_id !== $repo->id, 404);
    }
}
