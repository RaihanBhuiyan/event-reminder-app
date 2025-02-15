# Event Reminder App

A full-stack application for managing events with reminders, built with Laravel and Vue.js.

## Features

- CRUD operations for events
- Upcoming vs Completed events
- Auto-generated event IDs (EVT-0001 format)
- Offline support with sync
- Email reminders
- CSV import/export
- Responsive UI

## Technologies

- **Backend**: Laravel 10
- **Frontend**: Vue.js 3
- **Database**: MySQL
- **Styling**: Custom CSS

## Installation

### Prerequisites

- PHP 8.1+
- Composer
- Node.js 16+
- MySQL

### Steps

1. Clone repository:
   ```bash
   git clone https://github.com/RaihanBhuiyan/event-reminder-app.git
   cd event-reminder-app
   ```
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Configure environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Set up database:
   ```bash
   php artisan migrate --seed
   ```
5. Start development servers:
   ```bash
   php artisan serve
   npm run dev
   ```

### Configuration

Update the `.env` file with your:

```env
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=your_smtp_port
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_password
```

## API Endpoints

| Method | Endpoint           | Description            |
| ------ | ------------------ | ---------------------- |
| GET    | /api/events        | Get all events         |
| POST   | /api/events        | Create new event       |
| PUT    | /api/events/{id}   | Update event           |
| DELETE | /api/events/{id}   | Delete event           |
| POST   | /api/events/import | Import events from CSV |

## Email Integration

Update your `EventReminder` notification class:

```php
// app/Notifications/EventReminder.php

public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Event Reminder: ' . $this->event->title)
        ->line('You have an upcoming event:')
        ->line('**Title**: ' . $this->event->title)
        ->line('**Time**: ' . $this->event->reminder_time)
        ->action('View Event', url('/'))
        ->line('Manage your events: ' . url('/'))
        ->line('GitHub Repository: https://github.com/RaihanBhuiyan/event-reminder-app')
        ->line('Thank you for using our application!');
}
```

## License

MIT License

---

This README provides:

- Professional documentation
- Clear installation instructions
- API documentation
- Email integration details
- License information


