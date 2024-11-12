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
            $table->id(); // Auto-increment primary key
            $table->string('title'); // Event title
            $table->text('description')->nullable(); // Optional description
            $table->dateTime('start_datetime'); // Start date and time of the event
            $table->dateTime('end_datetime'); // End date and time of the event
            $table->string('color')->nullable(); // Optional color to differentiate events
            $table->boolean('all_day')->default(false); // Flag for all-day events
            $table->timestamps(); // Created and updated timestamps
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
