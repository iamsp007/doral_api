<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->enum('gender', ['male', 'female']);
            $table->string('address1', 100);
            $table->string('address2', 100)->default('NULL');
            $table->string('zip', 8);
            $table->bigInteger('phone')->unique();
            $table->string('email', 70)->unique();
            $table->date('dob');
            $table->string('ssn', 12);
            $table->string('place_of_examination', 50)->default('NULL');
            $table->date('date_of_examination')->nullable();
            $table->string('condition_state', 30)->default('NULL');
            $table->string('cin_no', 12)->default('NULL');
            $table->string('medicaid_number', 12);
            $table->string('medicare_number', 12);
            $table->foreignId('user_id')->index('user_id');
            $table->string('emg_first_name', 50);
            $table->string('emg_last_name', 50);
            $table->string('emg_address1', 100);
            $table->string('emg_address2', 100);
            $table->string('emg_zip', 8);
            $table->bigInteger('emg_phone');
            $table->string('emg_email', 70);
            $table->enum('status', ['active', 'inactive', 'discontinued', 'hold']);
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
        Schema::dropIfExists('patients');
    }
}
