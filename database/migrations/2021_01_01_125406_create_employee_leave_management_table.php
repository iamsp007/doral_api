<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeLeaveManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_leave_management', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('appointment_id')->nullable();
            $table->time('work_start_time')->nullable();
            $table->time('work_end_time')->nullable();
            $table->date('leave_date');
            $table->time('leave_start_time')->nullable();
            $table->time('leave_end_time')->nullable();
            $table->enum('leave_type',['1','2'])->default('1')->comment('1=full,2=half');
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
        Schema::dropIfExists('employee_leave_management');
    }
}
