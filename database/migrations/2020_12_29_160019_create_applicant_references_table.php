<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicant_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->index('applicant_id');
            $table->string('reference_name');
            $table->longText('reference_address')->nullable();
            $table->string('reference_phone')->nullable();
            $table->text('reference_relationship')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applicant_references');
    }
}
