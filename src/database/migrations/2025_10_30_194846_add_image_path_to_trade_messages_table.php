<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImagePathToTradeMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('trade_messages', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('body');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trade_messages', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
}
