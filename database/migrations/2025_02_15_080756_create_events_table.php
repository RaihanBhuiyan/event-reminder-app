<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('reminder_id')->unique(); // EVT-0001 format
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('reminder_time');
            $table->json('recipients'); // Store email array
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_reminder_sent')->default(false); // Add this line
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
