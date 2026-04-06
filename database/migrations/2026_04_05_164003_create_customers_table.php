<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('Customer', function (Blueprint $table) {
            $table->id('CustomerID');
            $table->string('CustomerName');
            $table->string('Phone');
            $table->string('Email')->unique();
            $table->string('Password');
            $table->text('Address');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Customer');
    }
}
