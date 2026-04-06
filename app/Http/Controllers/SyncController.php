<?php

namespace App\Http\Controllers;

use App\Jobs\SyncRepositoriesJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function refreshGitHub(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);

        SyncRepositoriesJob::dispatch($user->id);

        return back()->with('status', 'GitHub sync queued.');
    }
}
