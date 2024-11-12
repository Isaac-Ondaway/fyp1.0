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
        Schema::table('registration_tokens', function (Blueprint $table) {
            $table->bigInteger('created_by')->unsigned()->nullable()->after('expires_at');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('registration_tokens', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
    

};
