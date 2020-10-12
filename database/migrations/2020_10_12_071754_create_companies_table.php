<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('address1', 100);
            $table->string('address2', 100);
            $table->string('zip', 8);
            $table->string('email', 70);
            $table->bigInteger('phone');
            $table->string('npi', 30);
            $table->foreignId('np_id')->index('np_id');
            $table->foreignId('referal_id')->index('referal_id');
            $table->string('password');
            $table->foreignId('employee_id');
            $table->string('verification_comment', 500);
            $table->enum('status', ['Approve', 'Reject', 'Pending', 'Active']);
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
        Schema::dropIfExists('companies');
    }
}
