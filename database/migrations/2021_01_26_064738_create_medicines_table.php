<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id');
            $table->string('name',100);
            $table->integer('does');
            $table->integer('from');
            $table->integer('route');
            $table->string('amount');
            $table->string('class',30)->nullable();
            $table->integer('frequency')->nullable();
            $table->date('start_date');
            $table->date('order_date')->nullable();
            $table->date('taught_date')->nullable();
            $table->date('discontinue_date')->nullable();
            $table->date('discontinue_order_date')->nullable();
            $table->integer('preferred_pharmacy')->nullable();
            $table->string('comment')->nullable();
            $table->enum('is_new',['0','1'])->default('1')->comment('0=exists,1=new');
            $table->enum('status',['0','1'])->default('1')->comment('0=inactive,1=active');
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
        Schema::dropIfExists('medicines');
    }
}
