<?php

use Illuminate\Database\Seeder;

use App\Player;
use App\Point;
use App\PointPlayer;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $player1 = Player::create(['name' => 'オゴシ']);
        $player2 = Player::create(['name' => 'むーちょ']);
        $player3 = Player::create(['name' => 'こば']);
        $player4 = Player::create(['name' => 'さく']);
        $player5 = Player::create(['name' => 'かっちゃん']);

        $point = Point::create([]);

        $point->pointPlayers()->saveMany([
            new PointPlayer(['player_id' => $player1->id, 'point' => 100]),
            new PointPlayer(['player_id' => $player2->id, 'point' => 200]),
            new PointPlayer(['player_id' => $player3->id, 'point' => 300]),
            new PointPlayer(['player_id' => $player4->id, 'point' => 400]),
            new PointPlayer(['player_id' => $player5->id, 'point' => -1000]),
        ]);
    }
}
