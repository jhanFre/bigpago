<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use App\Loan;
use App\Payment;
use App\User;
use Faker\Generator as Faker;

$factory->define(Payment::class, function (Faker $faker) {
    return [
        'loan_id' => Loan::all()->random()->id,
        'client_id' => Client::all()->random()->id,
        'regular_user_id' => User::all()->random()->id,
        'type_payment' => $faker->randomElement([Payment::CREDIT, Payment::COUNTED]),
        'quantity' => $faker->word,
        'payment_date' => $faker->word,
        'state' => $faker->randomElement([Payment::PAIDFEES, Payment::PENDINGFEES, Payment::OVERDUEFEES]),
    ];
});
