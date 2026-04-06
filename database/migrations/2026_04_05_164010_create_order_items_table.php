<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('OrderItem', function (Blueprint $table) {
            $table->id('OrderItemID');
            $table->unsignedBigInteger('OrderID');
            $table->unsignedBigInteger('ProductID');
            $table->integer('Quantity');
            $table->decimal('UnitPrice', 10, 2);
            
            $table->foreign('OrderID')->references('OrderID')->on('Order');
            $table->foreign('ProductID')->references('ProductID')->on('Product');
        });
    }

    public function down()
    {
        Schema::dropIfExists('OrderItem');
    }
}
