<?php

namespace App\Services\Notifications;

use App\Enums\NotificationType;
use App\Models\User;
use App\Notifications\SystemEventNotification;

class NotificationService
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function notifyByType(
        User $user,
        NotificationType $type,
        array $context = [],
        ?string $actionUrl = null,
        ?string $actionLabel = null,
    ): void {
        $meta = $this->catalog()[$type->value] ?? [
            'title' => ucfirst(str_replace('_', ' ', $type->value)),
            'message' => 'A system event occurred.',
        ];

        $this->notify(
            $user,
            $type,
            $meta['title'],
            $meta['message'],
            $context,
            $actionUrl,
            $actionLabel,
        );
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function notify(
        User $user,
        NotificationType $type,
        string $title,
        string $message,
        array $context = [],
        ?string $actionUrl = null,
        ?string $actionLabel = null,
    ): void {
        $channels = $this->channelsFor($type);

        if ($channels === []) {
            return;
        }

        $user->notify(new SystemEventNotification(
            type: $type,
            title: $title,
            message: $message,
            channels: $channels,
            context: $context,
            actionUrl: $actionUrl,
            actionLabel: $actionLabel,
        ));
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function integrationConnected(User $user, string $provider, array $context = []): void
    {
        $this->notify(
            $user,
            NotificationType::IntegrationConnected,
            'Integration connected',
            sprintf('%s has been connected successfully.', ucfirst($provider)),
            $context,
        );
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function integrationDisconnected(User $user, string $provider, array $context = []): void
    {
        $this->notify(
            $user,
            NotificationType::IntegrationDisconnected,
            'Integration disconnected',
            sprintf('%s has been disconnected.', ucfirst($provider)),
            $context,
        );
    }

    public function manualSyncCompleted(User $user, int $repoCount): void
    {
        $this->notify(
            $user,
            NotificationType::ManualSyncCompleted,
            'Manual sync completed',
            sprintf('Manual sync finished. %d repositories were processed.', $repoCount),
            ['repositories_processed' => $repoCount],
        );
    }

    public function autoSyncSuccess(User $user, int $repoCount): void
    {
        $this->notify(
            $user,
            NotificationType::AutoSyncSuccess,
            'Auto-sync completed',
            sprintf('Auto-sync processed %d repositories with meaningful updates.', $repoCount),
            ['repositories_processed' => $repoCount],
        );
    }

    public function syncFailed(User $user, string $reason): void
    {
        $this->notify(
            $user,
            NotificationType::SyncFailed,
            'Sync failed',
            'Sync failed and needs attention: '.$reason,
            ['reason' => $reason],
            actionUrl: url('/'),
            actionLabel: 'Review integration',
        );
    }

    /**
     * @return array<int, string>
     */
    private function channelsFor(NotificationType $type): array
    {
        $channels = (array) config('gracesoft_notifications.default_channels', ['database']);
        $emailTypes = (array) config('gracesoft_notifications.email_types', []);

        if (in_array($type->value, $emailTypes, true)) {
            $channels[] = 'mail';
        }

        return array_values(array_unique($channels));
    }

    /**
     * @return array<string, array{title: string, message: string}>
     */
    private function catalog(): array
    {
        return [
            NotificationType::AccountCreated->value => ['title' => 'Welcome to GraceSoft Story', 'message' => 'Your account has been created successfully.'],
            NotificationType::LoginNewDevice->value => ['title' => 'New device sign-in detected', 'message' => 'A sign-in from a new device or location was detected.'],
            NotificationType::PasswordChanged->value => ['title' => 'Password changed', 'message' => 'Your password was changed successfully.'],
            NotificationType::EmailChanged->value => ['title' => 'Email changed', 'message' => 'Your account email address was updated.'],
            NotificationType::UnauthorizedAccessBlocked->value => ['title' => 'Unauthorized access blocked', 'message' => 'An unauthorized access attempt was blocked.'],

            NotificationType::TimelineCreated->value => ['title' => 'Timeline created', 'message' => 'A new timeline has been created.'],
            NotificationType::TimelineUpdated->value => ['title' => 'Timeline updated', 'message' => 'A significant timeline update was recorded.'],
            NotificationType::TimelineDeleted->value => ['title' => 'Timeline deleted', 'message' => 'A timeline was deleted.'],

            NotificationType::InvitedToTimeline->value => ['title' => 'Invited to timeline', 'message' => 'You have been invited to collaborate on a timeline.'],
            NotificationType::AccessGranted->value => ['title' => 'Access granted', 'message' => 'Your access was granted.'],
            NotificationType::AccessRevoked->value => ['title' => 'Access revoked', 'message' => 'Your access has been revoked.'],
            NotificationType::RolePermissionChanged->value => ['title' => 'Role or permission changed', 'message' => 'Your role or permissions have changed.'],

            NotificationType::IntegrationConnected->value => ['title' => 'Integration connected', 'message' => 'An integration has been connected successfully.'],
            NotificationType::IntegrationDisconnected->value => ['title' => 'Integration disconnected', 'message' => 'An integration has been disconnected.'],
            NotificationType::ManualSyncCompleted->value => ['title' => 'Manual sync completed', 'message' => 'Manual sync completed successfully.'],
            NotificationType::AutoSyncSuccess->value => ['title' => 'Auto-sync success', 'message' => 'Auto-sync completed with meaningful updates.'],
            NotificationType::SyncFailed->value => ['title' => 'Sync failed', 'message' => 'Sync failed and requires action.'],

            NotificationType::MilestoneCreated->value => ['title' => 'Milestone created', 'message' => 'A milestone has been created.'],
            NotificationType::MilestoneCompleted->value => ['title' => 'Milestone completed', 'message' => 'A milestone has been completed.'],
            NotificationType::SignificantProgressUpdate->value => ['title' => 'Significant progress update', 'message' => 'There is a significant project progress update.'],

            NotificationType::CriticalFailure->value => ['title' => 'Critical failure', 'message' => 'A critical failure occurred.'],
            NotificationType::DataConflictDetected->value => ['title' => 'Data conflict detected', 'message' => 'A data conflict was detected and may need review.'],
            NotificationType::ActionRequired->value => ['title' => 'Action required', 'message' => 'Action is required to continue.'],

            NotificationType::PasswordResetRequested->value => ['title' => 'Password reset requested', 'message' => 'A password reset was requested.'],
            NotificationType::SuspiciousActivityDetected->value => ['title' => 'Suspicious activity detected', 'message' => 'Suspicious account activity was detected.'],
            NotificationType::PrivacySettingChanged->value => ['title' => 'Privacy setting changed', 'message' => 'A privacy setting has changed.'],
            NotificationType::PassphraseUpdated->value => ['title' => 'Passphrase updated', 'message' => 'Your timeline passphrase was created or updated.'],

            NotificationType::SubscriptionStarted->value => ['title' => 'Subscription started', 'message' => 'Your subscription is now active.'],
            NotificationType::PaymentSuccessful->value => ['title' => 'Payment successful', 'message' => 'Your payment was successful.'],
            NotificationType::PaymentFailed->value => ['title' => 'Payment failed', 'message' => 'Your payment failed and needs attention.'],
            NotificationType::SubscriptionExpiring->value => ['title' => 'Subscription expiring', 'message' => 'Your subscription is expiring soon.'],
            NotificationType::SubscriptionCancelled->value => ['title' => 'Subscription cancelled', 'message' => 'Your subscription was cancelled.'],
            NotificationType::PlanChanged->value => ['title' => 'Plan updated', 'message' => 'Your plan has been updated.'],

            NotificationType::ImportantFeatureRelease->value => ['title' => 'Important feature release', 'message' => 'A major product release is now available.'],
            NotificationType::PolicyTermsUpdate->value => ['title' => 'Policy update', 'message' => 'Terms or policy updates are now in effect.'],
        ];
    }
}
