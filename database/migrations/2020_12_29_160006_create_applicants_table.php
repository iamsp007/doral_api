<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('other_name')->nullable();
            $table->string('ssn');
            $table->string('phone');
            $table->string('home_phone')->nullable();
            // $table->string('emergency_phone')->nullable();
            $table->date('date');
            $table->boolean('us_citizen')->default(0);
            $table->longText('immigration_id')->nullable();
            $table->longText('address_line_1')->nullable();
            $table->longText('address_line_2')->nullable();
            $table->foreignId('city')->index('city')->nullable();
            $table->foreignId('state')->index('state')->nullable();
            $table->string('zip')->nullable();
            $table->text('address_life')->nullable();
            $table->boolean('bonded')->default(0);
            $table->boolean('refused_bond')->default(0);
            $table->boolean('convicted_crime')->default(0);
            $table->string('emergency_name')->nullable();
            $table->longText('emergency_address')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->text('emergency_relationship')->nullable();
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
        Schema::dropIfExists('applicants');
    }
}
