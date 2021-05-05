<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index('user_id')->nullable();
            $table->enum('dose', ['first', 'second'])->default('first');
            $table->string('patient_name')->nullable();
            $table->string('phone')->nullable();
            $table->json('data')->nullable();
            $table->string('recipient_sign')->nullable();
            $table->string('interpreter_sign')->nullable();
            $table->string('vaccination_sign')->nullable();
            $table->enum('status', [0, 1])->default(1);
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
        Schema::dropIfExists('covid_forms');
    }
}
