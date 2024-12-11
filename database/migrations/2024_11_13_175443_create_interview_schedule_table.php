<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterviewScheduleTable extends Migration
{
    public function up()
    {
        Schema::create('interview_schedule', function (Blueprint $table) {
            $table->bigIncrements('schedule_id');
            $table->unsignedBigInteger('interviewee_id');
            $table->string('program_id', 255);
            $table->unsignedBigInteger('batch_id');
            $table->dateTime('scheduled_date');
            $table->string('slot');
            $table->text('remarks')->nullable();
            $table->enum('status', ['Pending', 'Scheduled', 'Attended', 'Absent', 'Accepted', 'Rejected'])->default('Scheduled');
            $table->timestamps();

            // Foreign keys
            $table->foreign('interviewee_id')->references('interviewID')->on('interviews')->onDelete('cascade');
            $table->foreign('program_id')->references('programID')->on('programs')->onDelete('cascade');
            $table->foreign('batch_id')->references('batchID')->on('batches')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('interview_schedule');
    }
}