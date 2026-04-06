<?php

namespace App\Enums;

enum NotificationType: string
{
    case AccountCreated = 'account_created';
    case LoginNewDevice = 'login_new_device';
    case PasswordChanged = 'password_changed';
    case EmailChanged = 'email_changed';
    case UnauthorizedAccessBlocked = 'unauthorized_access_blocked';

    case TimelineCreated = 'timeline_created';
    case TimelineUpdated = 'timeline_updated';
    case TimelineDeleted = 'timeline_deleted';

    case InvitedToTimeline = 'invited_to_timeline';
    case AccessGranted = 'access_granted';
    case AccessRevoked = 'access_revoked';
    case RolePermissionChanged = 'role_permission_changed';

    case IntegrationConnected = 'integration_connected';
    case IntegrationDisconnected = 'integration_disconnected';
    case ManualSyncCompleted = 'manual_sync_completed';
    case AutoSyncSuccess = 'auto_sync_success';
    case SyncFailed = 'sync_failed';

    case MilestoneCreated = 'milestone_created';
    case MilestoneCompleted = 'milestone_completed';
    case SignificantProgressUpdate = 'significant_progress_update';

    case CriticalFailure = 'critical_failure';
    case DataConflictDetected = 'data_conflict_detected';
    case ActionRequired = 'action_required';

    case PasswordResetRequested = 'password_reset_requested';
    case SuspiciousActivityDetected = 'suspicious_activity_detected';
    case PrivacySettingChanged = 'privacy_setting_changed';
    case PassphraseUpdated = 'passphrase_updated';

    case SubscriptionStarted = 'subscription_started';
    case PaymentSuccessful = 'payment_successful';
    case PaymentFailed = 'payment_failed';
    case SubscriptionExpiring = 'subscription_expiring';
    case SubscriptionCancelled = 'subscription_cancelled';
    case PlanChanged = 'plan_changed';

    case ImportantFeatureRelease = 'important_feature_release';
    case PolicyTermsUpdate = 'policy_terms_update';
}
