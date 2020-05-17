<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MoneyPlayer;
use Faker\Generator as Faker;

$factory->define(MoneyPlayer::class, function (Faker $faker) {
    return [
        'money' => $faker->randomNumber,
    ];
});
