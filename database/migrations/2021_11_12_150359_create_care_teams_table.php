<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCareTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('care_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->index('patient_id');
            $table->json('detail')->nullable();
            $table->enum('type', ['1', '2','3'])->comment('1=family,2=physician,3=pharmacy');
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
        Schema::dropIfExists('care_teams');
    }
}
