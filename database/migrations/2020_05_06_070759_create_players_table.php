<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->datetime('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('point_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('point_id');
            $table->unsignedBigInteger('player_id');
            $table->integer('point');

            $table->unique(['point_id', 'player_id']);
            $table->foreign('point_id')->references('id')->on('points');
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
        Schema::dropIfExists('point_player');
        Schema::dropIfExists('players');
    }
}
