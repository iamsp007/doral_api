<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RoadlInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roadl_information', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_requests_id');
            $table->integer('user_id');
            $table->integer('client_id');
            $table->string('latitude',255);
            $table->string('longitude',255);
            $table->enum('status',['start','running','complete'])->default('start');
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
        Schema::dropIfExists('roadl_information');
    }
}
