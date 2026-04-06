<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('Category', function (Blueprint $table) {
            $table->id('CategoryID');
            $table->string('CategoryName');
            $table->text('Description')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('Category');
    }
}
