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

        Schema::create('moneys', function (Blueprint $table) {
            $table->id();
            $table->datetime('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('money_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('money_id');
            $table->unsignedBigInteger('player_id');
            $table->integer('money')->default(0);

            $table->unique(['money_id', 'player_id']);
            $table->foreign('money_id')->references('id')->on('moneys')->onDelete('cascade');
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
        Schema::dropIfExists('money_players');
        Schema::dropIfExists('moneys');
        Schema::dropIfExists('players');
    }
}
