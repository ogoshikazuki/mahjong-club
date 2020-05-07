<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->datetime('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('game_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->integer('rate');
            $table->timestamps();

            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
        });

        Schema::create('game_result_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_result_id');
            $table->unsignedBigInteger('player_id');
            $table->integer('point');
            $table->timestamps();

            $table->foreign('game_result_id')->references('id')->on('game_results')->onDelete('cascade');
            $table->foreign('player_id')->references('id')->on('players');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_result_players');
        Schema::dropIfExists('game_results');
        Schema::dropIfExists('games');
    }
}
