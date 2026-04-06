<?php

use App\Enums\NotificationType;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists notifications for authenticated user', function () {
    $user = User::factory()->create();

    app(NotificationService::class)->notify(
        $user,
        NotificationType::TimelineUpdated,
        'Timeline updated',
        'A significant timeline update occurred.',
    );

    $response = $this->actingAs($user)->getJson('/notifications');

    $response->assertOk();
    $response->assertJsonStructure(['data']);
    expect(count($response->json('data')))->toBe(1);
});

it('marks single notification as read', function () {
    $user = User::factory()->create();

    app(NotificationService::class)->notify(
        $user,
        NotificationType::TimelineUpdated,
        'Timeline updated',
        'A significant timeline update occurred.',
    );

    $notification = $user->notifications()->firstOrFail();

    $response = $this->actingAs($user)
        ->postJson('/notifications/'.$notification->id.'/read');

    $response->assertOk();

    $notification->refresh();
    expect($notification->read_at)->not->toBeNull();
});

it('marks all notifications as read', function () {
    $user = User::factory()->create();

    app(NotificationService::class)->notify(
        $user,
        NotificationType::TimelineUpdated,
        'Timeline updated',
        'First update.',
    );

    app(NotificationService::class)->notify(
        $user,
        NotificationType::TimelineUpdated,
        'Timeline updated',
        'Second update.',
    );

    $response = $this->actingAs($user)->postJson('/notifications/read-all');

    $response->assertOk();

    expect($user->fresh()->unreadNotifications()->count())->toBe(0);
});
