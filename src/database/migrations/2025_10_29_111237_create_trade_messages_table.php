<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('trade_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('trade_rooms')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('edited_at')->nullable();
            $table->softDeletes(); // $table->timestamp('deleted_at')->nullable();のショートカット
            $table->timestamps();

            $table->index(['room_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('trade_messages');
    }
}
