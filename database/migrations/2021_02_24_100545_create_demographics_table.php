<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemographicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demographics', function (Blueprint $table) {
            $table->id();
            $table->string('doral_id')->nullable();
            $table->integer('user_id');
            $table->string('ssn')->nullable();
            
            $table->json('team')->nullable();
            $table->json('location')->nullable();
            $table->json('branch')->nullable();
            $table->json('accepted_services')->nullable();
            $table->json('address')->nullable();
            $table->json('language')->nullable();
            $table->enum('type', ['1', '2'])->comment('1=patient,2=caregiver');

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
        Schema::dropIfExists('demographics');
    }
}
