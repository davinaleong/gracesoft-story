<?php

use App\Enums\NotificationType;
use App\Models\User;
use App\Notifications\SystemEventNotification;
use App\Services\Notifications\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('sends default in-app notification for standard events', function () {
    Notification::fake();

    $user = User::factory()->create();

    app(NotificationService::class)->notify(
        $user,
        NotificationType::TimelineUpdated,
        'Timeline updated',
        'A timeline event happened.',
    );

    Notification::assertSentTo(
        $user,
        SystemEventNotification::class,
        function (SystemEventNotification $notification): bool {
            return $notification->type === NotificationType::TimelineUpdated
                && $notification->channels === ['database'];
        },
    );
});

it('adds email delivery for security and critical notification types', function () {
    Notification::fake();

    $user = User::factory()->create();

    app(NotificationService::class)->notify(
        $user,
        NotificationType::SyncFailed,
        'Sync failed',
        'Sync failed and needs attention.',
    );

    Notification::assertSentTo(
        $user,
        SystemEventNotification::class,
        function (SystemEventNotification $notification): bool {
            return $notification->type === NotificationType::SyncFailed
                && in_array('database', $notification->channels, true)
                && in_array('mail', $notification->channels, true);
        },
    );
});
