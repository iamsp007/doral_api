<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyFailImportRecodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('fail_import_recode')) {
             Schema::table('fail_import_recode', function (Blueprint $table) {
                $table->dropColumn('attribute');
                $table->dropColumn('values');
                $table->dropColumn('errors');
                $table->string('error',500);
            });
        }
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
