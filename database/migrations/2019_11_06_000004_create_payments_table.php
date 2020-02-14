<?php

use App\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('loan_id')->unsigned();
            $table->bigInteger('client_id')->unsigned();
            $table->bigInteger('regular_user_id')->unsigned();
            $table->string('type_payment')->default(Payment::CREDIT);
            $table->string('quantity');
            $table->string('payment_date');
            $table->string('state')->default(Payment::PENDINGFEES);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('loan_id')->references('id')->on('loans');
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
        Schema::dropIfExists('payments');
    }
}
