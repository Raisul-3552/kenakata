<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('ProductDetails', function (Blueprint $table) {
            $table->unsignedBigInteger('ProductID')->primary();
            $table->text('Description');
            $table->text('Specification');
            $table->string('Warranty')->nullable();
            $table->string('Image')->nullable();
            
            $table->foreign('ProductID')->references('ProductID')->on('Product')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ProductDetails');
    }
}
