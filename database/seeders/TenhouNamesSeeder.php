<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenhouNamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tenhou_names')->insert([
            ['name' => 'hachi@', 'player_id' => 3],
            ['name' => 'かつひっげ', 'player_id' => 5],
            ['name' => 'まもちゃんや', 'player_id' => 6],
        ]);
    }
}
