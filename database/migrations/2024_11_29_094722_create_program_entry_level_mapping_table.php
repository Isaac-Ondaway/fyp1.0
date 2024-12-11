<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramEntryLevelMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_entry_level_mapping', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary key
            $table->string('programID'); // Foreign key to programs table
            $table->unsignedBigInteger('batchID'); // Foreign key to programs table
            $table->unsignedBigInteger('entry_level_category_id'); // Foreign key to entry_level_categories table
            $table->boolean('is_offered')->default(false); // Checkbox state (offered or not)
            $table->timestamps();

            // Foreign key constraints
            $table->foreign(['programID', 'batchID'])
                  ->references(['programID', 'batchID'])
                  ->on('programs')
                  ->onDelete('cascade');
            $table->foreign('entry_level_category_id')
                  ->references('entryLevelCategoryID')
                  ->on('entry_level_categories')
                  ->onDelete('cascade');

            // Add unique constraint to avoid duplicate mappings for the same program, batch, and entry level
            $table->unique(['programID', 'batchID', 'entry_level_category_id'], 'unique_program_batch_entry_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('program_entry_level_mapping');
    }
}
