<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('trade_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rater_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ratee_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('stars');
            $table->timestamps();

            $table->unique(['purchase_id', 'rater_id']);
            $table->index(['purchase_id', 'ratee_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('trade_ratings');
    }
}
