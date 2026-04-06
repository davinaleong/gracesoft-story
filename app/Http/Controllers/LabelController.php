<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $user->labels()->create([
            'name' => $validated['name'],
            'color' => $validated['color'] ?? '#6366f1',
        ]);

        return back()->with('status', 'Label created.');
    }

    public function update(Request $request, Label $label): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);
        abort_if($label->user_id !== $user->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $label->update([
            'name' => $validated['name'],
            'color' => $validated['color'] ?? $label->color,
        ]);

        return back()->with('status', 'Label updated.');
    }

    public function destroy(Request $request, Label $label): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);
        abort_if($label->user_id !== $user->id, 404);

        $label->delete();

        return back()->with('status', 'Label deleted.');
    }
}
