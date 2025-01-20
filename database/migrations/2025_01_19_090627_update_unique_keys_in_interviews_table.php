<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUniqueKeysInInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interviews', function (Blueprint $table) {
            // Add unique constraint for programID, batchID, and contactNumber
            $table->unique(['programID', 'batchID', 'contactNumber'], 'unique_program_batch_contact');

            // Add unique constraint for programID, batchID, and email
            $table->unique(['programID', 'batchID', 'email'], 'unique_program_batch_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interviews', function (Blueprint $table) {
            // Drop the unique constraints
            $table->dropUnique('unique_program_batch_contact');
            $table->dropUnique('unique_program_batch_email');
        });
    }
}
