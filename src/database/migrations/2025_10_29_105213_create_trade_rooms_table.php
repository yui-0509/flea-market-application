<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('trade_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique('purchase_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('trade_rooms');
    }
}
