<?php

use App\Loan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('client_id')->unsigned();
            $table->bigInteger('regular_user_id')->unsigned();
            $table->string('type_loan');
            $table->string('quantity');
            $table->string('interests');
            $table->string('number_fees');
            $table->string('total');
            $table->string('init_date');
            $table->string('payment_dates')->default(Loan::BIWEEKLY);
            $table->string('state')->default(Loan::PROCESSLOAN);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('regular_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
