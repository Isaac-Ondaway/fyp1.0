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
        Schema::table('chats', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('message')->nullable();
    
            // Add foreign key constraints
            $table->foreign('session_id')->references('session_id')->on('chat_sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['session_id', 'user_id', 'message']);
        });
    }
    
};
