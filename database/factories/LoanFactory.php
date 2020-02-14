<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use App\Loan;
use App\User;
use Faker\Generator as Faker;

$factory->define(Loan::class, function (Faker $faker) {
    return [
        'client_id' => Client::all()->random()->id,
        'regular_user_id' => User::all()->random()->id,
        'type_loan' => $faker->word,
        'quantity' => $faker->word,
        'interests' => $faker->word,
        'number_fees' => $faker->word,
        'total' => $faker->word,
        'init_date' => $faker->word,
        'payment_dates' => $faker->randomElement([Loan::BIWEEKLY, Loan::WEEKLY, Loan::DAILY]),
        'state' => $faker->randomElement([Loan::PAIDLOAN, Loan::PROCESSLOAN, Loan::OVERDUELOAN]),
    ];
});