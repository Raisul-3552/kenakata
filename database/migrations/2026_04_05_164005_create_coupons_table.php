<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    public function up()
    {
        Schema::create('Coupon', function (Blueprint $table) {
            $table->id('CouponID');
            $table->string('CouponCode')->unique();
            $table->decimal('DiscountAmount', 10, 2);
            $table->date('StartDate');
            $table->date('EndDate');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Coupon');
    }
}
