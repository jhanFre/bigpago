<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use App\User;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    return [
        'regular_user_id' => User::all()->random()->id,
        'name' => $faker->name,
        'surname' => $faker->name,
        'type_document' => $faker->word,
        'document' => $faker->word,
        'sex' => $faker->word,
        'address' => $faker->word,
        'phone' => $faker->word,
        'state' => $faker->randomElement([Client::ACTIVECLIENT, Client::REPORTEDCLIENT]),
    ];
});
