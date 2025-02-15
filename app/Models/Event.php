<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($event) {
            $latest = Event::orderBy('id', 'desc')->first();
            $sequence = $latest ? (int) str_replace('EVT-', '', $latest->reminder_id) + 1 : 1;
            $event->reminder_id = 'EVT-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        });
    }

    protected $fillable = [
        'reminder_id',
        'title',
        'description',
        'reminder_time',
        'recipients',
        'is_completed',
        'is_reminder_sent', // Add this line
    ];

    protected $casts = [
        'reminder_time' => 'datetime',
        'recipients' => 'array',
        'is_completed' => 'boolean',
        'is_reminder_sent' => 'boolean', // Add this line
    ];
}
