<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignPatientToCaseManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_patient_to_case_managers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->index('employee_id');
            $table->foreignId('patient_id')->index('patient_id');
            $table->string('comment', 250);
            $table->enum('status', ['active', 'inactive']);
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
        Schema::dropIfExists('assign_patient_to_case_managers');
    }
}
