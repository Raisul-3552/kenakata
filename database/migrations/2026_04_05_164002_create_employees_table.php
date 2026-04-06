<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('Employee', function (Blueprint $table) {
            $table->id('EmployeeID');
            $table->unsignedBigInteger('AdminID');
            $table->string('EmployeeName');
            $table->string('Phone');
            $table->string('Email')->unique();
            $table->string('Password');
            $table->text('Address');
            
            $table->foreign('AdminID')->references('AdminID')->on('Admin');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Employee');
    }
}
