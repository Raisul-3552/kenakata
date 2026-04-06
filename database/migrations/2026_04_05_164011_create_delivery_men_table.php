<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryMenTable extends Migration
{
    public function up()
    {
        Schema::create('DeliveryMan', function (Blueprint $table) {
            $table->id('DelManID');
            $table->string('DelManName');
            $table->string('Phone');
            $table->string('Email')->unique();
            $table->string('Password');
            $table->text('Address');
            $table->string('Status', 20)->default('Available');
        });
    }

    public function down()
    {
        Schema::dropIfExists('DeliveryMan');
    }
}
