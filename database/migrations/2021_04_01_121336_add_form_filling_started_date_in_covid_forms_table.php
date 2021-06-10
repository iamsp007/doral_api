<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormFillingStartedDateInCovidFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('covid_forms', function (Blueprint $table) {
            $table->dateTime('form_filling_date')->after('status')->comment('The datetime when form filling started');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('covid_forms', function (Blueprint $table) {
            $table->dropColumn('form_filling_date');
        });
    }
}
