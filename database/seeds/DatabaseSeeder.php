<?php

use Illuminate\Database\Seeder;

use App\Player;
use App\Money;
use App\MoneyPlayer;

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

        $point = Money::create([]);

        $point->moneyPlayers()->saveMany([
            new MoneyPlayer(['player_id' => $player1->id, 'money' => 100]),
            new MoneyPlayer(['player_id' => $player2->id, 'money' => 200]),
            new MoneyPlayer(['player_id' => $player3->id, 'money' => 300]),
            new MoneyPlayer(['player_id' => $player4->id, 'money' => 400]),
            new MoneyPlayer(['player_id' => $player5->id, 'money' => -1000]),
        ]);
    }
}
