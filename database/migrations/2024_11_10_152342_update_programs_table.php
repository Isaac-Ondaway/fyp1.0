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
        Schema::table('programs', function (Blueprint $table) {
            // Change programFee to text
            $table->text('programFee')->change();
            
            // Add new columns
            $table->string('studyProgram');
            $table->boolean('isInterviewExam');
            $table->boolean('isUjianMedsi');
            $table->boolean('isRayuan');
            $table->boolean('isDDegree');
            $table->boolean('learnMod');
            $table->boolean('isBumiputera');
            $table->boolean('isTEVT');
            $table->boolean('isKompetitif');
            $table->boolean('isBTECH');
            $table->boolean('isOKU');
        });
    }
    
    public function down()
    {
        Schema::table('programs', function (Blueprint $table) {
            // Revert programFee to decimal
            $table->decimal('programFee', 8, 2)->change();
    
            // Drop the new columns
            $table->dropColumn([
                'studyProgram',
                'isInterviewExam',
                'isUjianMedsi',
                'isRayuan',
                'isDDegree',
                'learnMod',
                'isBumiputera',
                'isTEVT',
                'isKompetitif',
                'isBTECH',
                'isOKU',
            ]);
        });
    }
    
};
