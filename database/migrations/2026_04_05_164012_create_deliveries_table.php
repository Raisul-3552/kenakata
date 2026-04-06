<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    public function up()
    {
        Schema::create('Delivery', function (Blueprint $table) {
            $table->id('DeliveryID');
            $table->unsignedBigInteger('OrderID');
            $table->unsignedBigInteger('DelManID');
            $table->string('DeliveryStatus')->default('Pending');
            $table->date('DeliveryDate')->nullable();
            
            $table->foreign('OrderID')->references('OrderID')->on('Order');
            $table->foreign('DelManID')->references('DelManID')->on('DeliveryMan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Delivery');
    }
}
