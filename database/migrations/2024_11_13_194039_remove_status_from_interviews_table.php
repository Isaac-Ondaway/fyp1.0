<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveStatusFromInterviewsTable extends Migration
{
    public function up()
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn('interviewStatus');
        });
    }

    public function down()
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->enum('interviewStatus', ['Pending', 'Completed', 'Cancelled'])->default('Pending');
        });
    }
}
