<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewcloumnsToCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('services', 255)->nullable();
            $table->string('fax_no', 15)->nullable();
            $table->string('administrator_name', 45)->nullable();
            $table->string('registration_no', 191)->nullable();
            $table->string('administrator_emailId', 191)->nullable();
            $table->string('licence_no', 45)->nullable();
            $table->string('administrator_phone_no', 45)->nullable();
            $table->string('insurance_id', 45)->nullable();
            $table->date('expiration_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
}
