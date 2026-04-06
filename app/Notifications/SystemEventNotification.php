<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<int, string>  $channels
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public readonly NotificationType $type,
        public readonly string $title,
        public readonly string $message,
        public readonly array $channels,
        public readonly array $context = [],
        public readonly ?string $actionUrl = null,
        public readonly ?string $actionLabel = null,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->view('emails.notifications.system-event', [
                'title' => $this->title,
                'message' => $this->message,
                'context' => $this->context,
                'actionUrl' => $this->actionUrl,
                'actionLabel' => $this->actionLabel,
                'recipientName' => $notifiable->name ?? 'there',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type->value,
            'title' => $this->title,
            'message' => $this->message,
            'context' => $this->context,
            'action_url' => $this->actionUrl,
            'action_label' => $this->actionLabel,
        ];
    }
}
