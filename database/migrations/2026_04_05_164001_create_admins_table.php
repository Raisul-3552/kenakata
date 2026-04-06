<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Admin', function (Blueprint $table) {
            $table->id('AdminID');
            $table->string('AdminName');
            $table->string('Email')->unique();
            $table->string('Password');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Admin');
    }
}
