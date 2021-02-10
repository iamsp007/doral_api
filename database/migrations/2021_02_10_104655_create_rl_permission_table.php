<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRlPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('rl_permission')) {
            Schema::create('rl_permission', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('rl_module_name_id');
                $table->string('name');
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('rl_module_name_id')->references('id')->on('rl_module_name')->onDelete('cascade'); 
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rl_permission');
    }
}
