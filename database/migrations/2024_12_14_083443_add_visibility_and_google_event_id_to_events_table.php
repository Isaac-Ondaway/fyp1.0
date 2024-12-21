<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibilityAndGoogleEventIdToEventsTable extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('visibility', ['public', 'private'])->default('private')->after('color');
            $table->string('google_event_id')->nullable()->after('visibility');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('visibility');
            $table->dropColumn('google_event_id');
        });
    }
}