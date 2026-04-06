<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeEmployeeIdNullableInProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE Product ALTER COLUMN EmployeeID BIGINT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE Product ALTER COLUMN EmployeeID BIGINT NOT NULL');
    }
}
