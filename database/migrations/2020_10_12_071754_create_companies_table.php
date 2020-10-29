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
            $table->string('address1', 100)->nullable();
            $table->string('address2', 100)->nullable();
            $table->string('zip', 8)->nullable();
            $table->string('email', 70);
            $table->bigInteger('phone');
            $table->string('npi', 30)->nullable();
            $table->foreignId('np_id')->nullable();
            $table->foreignId('referal_id')->index('referal_id');
            $table->string('password')->nullable();
            $table->string('verification_comment', 500);
            $table->enum('status', ['approve', 'reject', 'pending', 'active']);
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
