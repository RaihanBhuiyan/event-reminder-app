<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EventReminder;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = Event::where('reminder_time', '<=', now())
            ->where('is_completed', false)
            ->where('is_reminder_sent', false)
            ->get();

        foreach ($events as $event) {
            foreach ($event->recipients as $email) {
                Notification::route('mail', $email)
                    ->notify(new EventReminder($event));
            }
            $event->update(['is_reminder_sent' => true]);
        }
    }
}
