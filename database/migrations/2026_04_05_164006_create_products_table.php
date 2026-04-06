<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('Product', function (Blueprint $table) {
            $table->id('ProductID');
            $table->unsignedBigInteger('EmployeeID');
            $table->unsignedBigInteger('CategoryID');
            $table->string('ProductName');
            $table->string('Brand');
            $table->decimal('Price', 10, 2);
            $table->integer('Stock');
            
            $table->foreign('EmployeeID')->references('EmployeeID')->on('Employee');
            $table->foreign('CategoryID')->references('CategoryID')->on('Category');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Product');
    }
}
