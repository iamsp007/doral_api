<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->boolean('medicare_enrolled')->default(0);
            $table->foreignId('medicare_state')->index('medicare_state')->nullable();
            $table->string('medicare_number')->nullable();
            $table->boolean('medicaid_enrolled')->default(0);
            $table->foreignId('medicaid_state')->index('medicaid_state')->nullable();
            $table->string('medicaid_number')->nullable();
            $table->string('federal_dea_id')->nullable();
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
        Schema::dropIfExists('certificates');
    }
}
