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
            $table->string('first_name',30)->nullable();
            $table->string('last_name',30)->nullable();
            $table->enum('gender', ['1', '2','3'])->comment('1=male,2=female,3=other');
            $table->date('dob')->nullable();
            $table->bigInteger('phone')->nullable()->unique();
            $table->string('email',50)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('status', ['0', '1', '2', '3'])->default('0')->comment('0=pending,1=active,2=inactive,3=reject');
            $table->rememberToken();
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
