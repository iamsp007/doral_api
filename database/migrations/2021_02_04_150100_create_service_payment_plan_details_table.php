<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicePaymentPlanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_payment_plan_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_payment_plan_id');
            $table->foreign('service_payment_plan_id')->references('id')->on('service_payment_plan')->onDelete('cascade');
            $table->string('name',100)->nullable();
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
        Schema::dropIfExists('service_payment_plan_details');
    }
}
