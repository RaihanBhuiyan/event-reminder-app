<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminder extends Notification
{
    use Queueable;

    /**
     * The event instance.
     *
     * @var object
     */
    protected $event;

    /**
     * Create a new notification instance.
     *
     * @param object $event
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Event Reminder: ' . $this->event->title)
            ->line('You have an upcoming event:')
            ->line('Title: ' . $this->event->title)
            ->line('Time: ' . $this->event->reminder_time)
            ->action('View Event', url('/'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
