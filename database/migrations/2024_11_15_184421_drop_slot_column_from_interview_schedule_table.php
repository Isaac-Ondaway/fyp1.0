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
            $table->dropColumn('slot');
        });
    }
    
    public function down()
    {
        Schema::table('interview_schedule', function (Blueprint $table) {
            $table->string('slot')->nullable();
        });
    }
    
};
