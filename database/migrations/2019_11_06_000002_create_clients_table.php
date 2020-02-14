<?php

use App\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('regular_user_id')->unsigned();
            $table->string('name');
            $table->string('surname');
            $table->string('type_document');
            $table->string('document');
            $table->string('sex');
            $table->string('address');
            $table->string('phone');
            $table->string('state')->default(Client::ACTIVECLIENT);
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('clients');
    }
}
