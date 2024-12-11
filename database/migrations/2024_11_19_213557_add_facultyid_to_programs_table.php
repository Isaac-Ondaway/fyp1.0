<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacultyidToProgramsTable extends Migration
{
    public function up()
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->unsignedBigInteger('facultyID')->nullable(); // Add the column
            $table->foreign('facultyID') // Define the foreign key
                  ->references('id')
                  ->on('faculty')
                  ->onDelete('cascade'); // Add cascading delete behavior
        });
    }

    public function down()
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['facultyID']); // Drop the foreign key
            $table->dropColumn('facultyID');    // Drop the column
        });
    }
}
