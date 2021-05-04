<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyPaymentPlanInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_payment_plan_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->unsignedBigInteger('service_payment_plan_id');
            $table->foreign('service_payment_plan_id')->references('id')->on('service_payment_plan')->onDelete('cascade');
            $table->unsignedBigInteger('service_payment_plan_details_id');
            $table->foreign('service_payment_plan_details_id')->references('id')->on('service_payment_plan_details')->onDelete('cascade')->index('company_payment_plan_info_service_payment_details_id_foreign');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
        Schema::dropIfExists('company_payment_plan_info');
    }
}
