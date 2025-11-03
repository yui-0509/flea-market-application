<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeRoomParticipantsTable extends Migration
{
    public function up()
    {
        Schema::create('trade_room_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('trade_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'user_id']);
            $table->index(['user_id', 'last_read_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('trade_room_participants');
    }
}
