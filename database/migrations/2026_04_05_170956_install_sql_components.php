<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InstallSqlComponents extends Migration
{
    public function up()
    {
        $path3 = base_path('database/sql/03_stored_procedures.sql');
        $path4 = base_path('database/sql/04_views.sql');
        $path5 = base_path('database/sql/05_triggers.sql');

        if (file_exists($path3)) {
            $sql = file_get_contents($path3);
            $statements = explode('GO', $sql);
            foreach ($statements as $statement) {
                if(trim($statement)) DB::unprepared(trim($statement));
            }
        }
        if (file_exists($path4)) {
            $sql = file_get_contents($path4);
            $statements = explode('GO', $sql);
            foreach ($statements as $statement) {
                if(trim($statement)) DB::unprepared(trim($statement));
            }
        }
        if (file_exists($path5)) {
            $sql = file_get_contents($path5);
            $statements = explode('GO', $sql);
            foreach ($statements as $statement) {
                if(trim($statement)) DB::unprepared(trim($statement));
            }
        }
    }

    public function down()
    {
        // Add drop commands if needed
    }
}
