<?php

use App\Enums\NotificationType;

return [
    'default_channels' => ['database'],

    'email_types' => [
        NotificationType::LoginNewDevice->value,
        NotificationType::UnauthorizedAccessBlocked->value,
        NotificationType::AccessGranted->value,
        NotificationType::AccessRevoked->value,
        NotificationType::RolePermissionChanged->value,
        NotificationType::SyncFailed->value,
        NotificationType::CriticalFailure->value,
        NotificationType::ActionRequired->value,
        NotificationType::PasswordResetRequested->value,
        NotificationType::SuspiciousActivityDetected->value,
        NotificationType::PrivacySettingChanged->value,
        NotificationType::PassphraseUpdated->value,
        NotificationType::SubscriptionStarted->value,
        NotificationType::PaymentSuccessful->value,
        NotificationType::PaymentFailed->value,
        NotificationType::SubscriptionExpiring->value,
        NotificationType::SubscriptionCancelled->value,
        NotificationType::PlanChanged->value,
        NotificationType::PolicyTermsUpdate->value,
    ],
];
