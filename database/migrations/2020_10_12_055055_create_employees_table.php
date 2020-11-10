<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->enum('gender', ['male', 'female']);
            $table->string('address1', 100)->nullable();
            $table->string('address2', 100)->nullable();
            $table->string('zip', 8)->nullable();
            $table->bigInteger('phone')->unique();
            $table->string('email', 70)->unique();
            $table->date('dob');
            $table->string('ssn', 12)->nullable();
            $table->string('npi', 12)->comment('This is optional field')->nullable();
            $table->foreignId('role_id')->index('role_id')->nullable();
            $table->foreignId('designation_id')->index('designation_id')->nullable();
            $table->foreignId('user_id')->index('user_id');
            $table->string('emg_first_name', 50)->nullable();
            $table->string('emg_last_name', 50)->nullable();
            $table->string('emg_address1', 100)->nullable();
            $table->string('emg_address2', 100)->nullable();
            $table->string('emg_zip', 8)->nullable();
            $table->bigInteger('emg_phone')->nullable();
            $table->string('emg_email', 70)->nullable();
            $table->date('join_date')->nullable();
            $table->enum('employeement _type', ['applicant', 'employee', 'rejected']);
            $table->enum('status', ['active', 'inactive', 'applicant', 'employee']);
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
        Schema::dropIfExists('employees');
    }
}
