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
        Schema::table('interview_schedule', function (Blueprint $table) {
            $table->dateTime('scheduled_date')->change();
        });
    }
    
    public function down()
    {
        Schema::table('interview_schedule', function (Blueprint $table) {
            $table->date('scheduled_date')->change();
        });
    }
    
};
