<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public array $payload) {}

    public function via($notifiable)
    {
        // DB by default, add 'mail' if you configure mailer
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->payload['title'] ?? 'Task Reminder',
            'body'  => $this->payload['body'] ?? '',
            'task_id' => $this->payload['task_id'] ?? null,
            'due'   => $this->payload['due'] ?? null,
        ];
    }
}
