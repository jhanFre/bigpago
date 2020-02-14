<?php

use App\Client;
use App\Loan;
use App\Payment;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::truncate();

        User::flushEventListeners();

        User::truncate();
        Client::truncate();
        Loan::truncate();
        Payment::truncate();

        User::flushEventListeners();
        Client::flushEventListeners();
        Loan::flushEventListeners();
        Payment::flushEventListeners();

        factory(User::class, 100)->create();
        factory(Client::class, 100)->create();
        factory(Loan::class, 100)->create();
        factory(Payment::class, 100)->create();
    }
}
