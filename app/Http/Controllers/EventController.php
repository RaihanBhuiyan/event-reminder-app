<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Get all events
    public function index()
    {
        return Event::all();
    }

    // Create a new event
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'reminder_time' => 'required|date',
            'recipients' => 'required|array',
        ]);

        return Event::create($request->all());
    }

    // Update an event (e.g., mark as completed)
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'is_completed' => 'boolean',
        ]);

        $event->update($request->all());
        return $event;
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt']);

        $file = $request->file('file');
        $csvData = array_map('str_getcsv', file($file));
        $data = [];

        foreach ($csvData as $row) {
            // Skip the header row
            if ($row[0] === 'title') continue;

            // Parse the date manually
            $reminderTime = \DateTime::createFromFormat('m/d/Y H:i', $row[2]);
            if (!$reminderTime) {
                return redirect()->back()->withErrors(['file' => "Invalid date format: {$row[2]}"]);
            }
            $data[] = [
                'title' => $row[0],
                'description' => $row[1],
                'reminder_time' => $reminderTime->format('Y-m-d H:i:s'), // Convert to MySQL format
                'recipients' => json_encode(explode(',', $row[3])),
            ];
            Event::create([
                'title' => $row[0],
                'description' => $row[1],
                'reminder_time' => $reminderTime->format('Y-m-d H:i:s'), // Convert to MySQL format
                'recipients' => json_encode(explode(',', $row[3])),
            ]);
        }

        return redirect()->back()->with('success', 'Events imported successfully!');
    }
}
