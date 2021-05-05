<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditFailImportRecodeTable extends Migration
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
              
                $table->dropColumn('error');
                $table->string('errors',500)->after('file_name');
                $table->string('attribute',500)->after('file_name');
                $table->string('values',500)->after('file_name');
                $table->string('service_id')->after('file_name');
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
