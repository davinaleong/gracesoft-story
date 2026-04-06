<?php

namespace App\Http\Controllers;

use App\Enums\NotificationType;
use App\Models\Label;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function store(Request $request, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $label = $user->labels()->create([
            'name' => $validated['name'],
            'color' => $validated['color'] ?? '#6366f1',
        ]);

        $notifications->notify(
            $user,
            NotificationType::TimelineUpdated,
            'Timeline updated',
            'A new label has been created.',
            ['label' => $label->name],
        );

        return back()->with('status', 'Label created.');
    }

    public function update(Request $request, Label $label, NotificationService $notifications): RedirectResponse
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

        $notifications->notify(
            $user,
            NotificationType::TimelineUpdated,
            'Timeline updated',
            'A label has been updated.',
            ['label' => $label->name],
        );

        return back()->with('status', 'Label updated.');
    }

    public function destroy(Request $request, Label $label, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);
        abort_if($label->user_id !== $user->id, 404);

        $labelName = $label->name;
        $label->delete();

        $notifications->notify(
            $user,
            NotificationType::TimelineUpdated,
            'Timeline updated',
            'A label has been deleted.',
            ['label' => $labelName],
        );

        return back()->with('status', 'Label deleted.');
    }
}
