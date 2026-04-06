<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    public function up()
    {
        Schema::create('Offer', function (Blueprint $table) {
            $table->id('OfferID');
            $table->unsignedBigInteger('ProductID');
            $table->decimal('DiscountAmount', 10, 2);
            $table->date('StartDate');
            $table->date('EndDate');
            
            $table->foreign('ProductID')->references('ProductID')->on('Product');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Offer');
    }
}
