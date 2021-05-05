<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnEmailVerifiedToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('companies')) {
            if (!Schema::hasColumn('companies', 'email_verified')) {
                Schema::table('companies', function($table) {
                    $table->enum('email_verified', ['0', '1'])->after('expiration_date')->default('0')->comment('0=unverified,1=verified');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
    }
}
