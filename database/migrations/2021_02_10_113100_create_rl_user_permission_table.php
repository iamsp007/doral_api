<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRlUserPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         if (!Schema::hasTable('rl_user_permission')) {

             Schema::create('rl_user_permission', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('rl_permission_id');
                $table->enum('user_tabel_name', ['1', '2'])->comment('1=users,2=companies');
               $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->foreign('rl_permission_id')->references('id')->on('rl_permission')->onDelete('cascade'); 
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
        Schema::dropIfExists('rl_user_permission');
    }
}
