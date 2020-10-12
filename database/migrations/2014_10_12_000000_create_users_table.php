<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->bigInteger('phone');
            $table->enum('type', ['employee', 'patient'])->comment('If user is Patient: patient, Employee:employee');;
            $table->foreignId('employee_id')->index('employee_id')->comment('Referance with Employee Table');
            $table->foreignId('patient_id')->index('patient_id')->comment('REferance with Patients Table');            
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('status', ['active', 'inactive', 'applicant', 'employee']);
            $table->rememberToken();
            $table->enum('level', ['easy', 'hard']);
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
        Schema::dropIfExists('users');
    }
}
