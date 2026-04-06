<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('Order', function (Blueprint $table) {
            $table->id('OrderID');
            $table->unsignedBigInteger('CustomerID');
            $table->unsignedBigInteger('EmployeeID')->nullable();
            $table->unsignedBigInteger('CouponID')->nullable();
            $table->string('OrderStatus')->default('Pending');
            $table->decimal('TotalAmount', 10, 2);
            $table->date('OrderDate');
            $table->text('Address');
            
            $table->foreign('CustomerID')->references('CustomerID')->on('Customer');
            $table->foreign('EmployeeID')->references('EmployeeID')->on('Employee');
            $table->foreign('CouponID')->references('CouponID')->on('Coupon');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Order');
    }
}
