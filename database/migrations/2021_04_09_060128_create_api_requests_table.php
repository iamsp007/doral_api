<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->index('company_id');
            $table->foreignId('software_id')->index('software_id');
            $table->json('authentication_field');
            $table->foreignId('api_id')->index('api_id');
            $table->json('search_field');
            $table->enum('schedule', [1 ,2 ,3 ,4])->comment('1=Daily,2=Weekly,3=Monthly,4=Quarterly')->default(1);
            $table->string('extra_schedule');
            $table->enum('status', [0, 1])->comment('0=inactive,1=active')->default(1);
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
        Schema::dropIfExists('api_requests');
    }
}
