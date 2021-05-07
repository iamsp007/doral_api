<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeEnumTypeForTypeAndGenterAtBirth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demographics', function (Blueprint $table) {
        
            DB::statement("ALTER TABLE demographics CHANGE COLUMN gender_at_birth gender_at_birth ENUM('1', '2', '3', '4') NOT NULL DEFAULT '4'");
            DB::statement("ALTER TABLE demographics CHANGE COLUMN type type ENUM('1', '2', '3') NOT NULL DEFAULT '1'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demographics', function (Blueprint $table) {
            //
        });
    }
}
