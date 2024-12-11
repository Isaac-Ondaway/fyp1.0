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
    Schema::create('tickets', function (Blueprint $table) {
        $table->id('ticket_id');
        $table->unsignedBigInteger('user_id');
        $table->string('subject');
        $table->text('description');
        $table->enum('status', ['Open', 'In Progress', 'Resolved'])->default('Open');
        $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
