<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoreDetailFromApplicant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->json('military_detail')->nullable()->after('family_detail');
            $table->json('security_detail')->nullable()->after('military_detail');
            $table->json('address_detail')->nullable()->after('security_detail');
            $table->json('prior_detail')->nullable()->after('address_detail');
            $table->json('reference_detail')->nullable()->after('prior_detail');
            $table->json('employer_detail')->nullable()->after('reference_detail');
            $table->json('education_detail')->nullable()->after('employer_detail');
            $table->json('language_detail')->nullable()->after('education_detail');
            $table->json('skil_detail')->nullable()->after('language_detail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applicants', function (Blueprint $table) {
            //
        });
    }
}
