<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientPlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_players', function (Blueprint $table) {
            $table->id();
            $table->string('payer_name');
            $table->string('payer_coordinator_id');
            $table->string('payer_coordinator_name');
            $table->string('patient_status_id');
            $table->string('patient_status_name');
            
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
        Schema::dropIfExists('patient_players');
    }
}
