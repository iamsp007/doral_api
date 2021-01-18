<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecuritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->after('id')->nullable();
            $table->longText('welcome_message')->after('is_available')->nullable();
        });

        Schema::create('securities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index('user_id');
            $table->string('security_question');
            $table->longText('security_answer')->nullable();
            $table->boolean('background_check')->default(0)->comment('0=FALSE, 1=TRUE');
            $table->boolean('diclosure_agreement')->default(0)->comment('0=FALSE, 1=TRUE');
            $table->boolean('ocg_agreement')->default(0)->comment('0=FALSE, 1=TRUE');
            $table->boolean('authorization')->default(0)->comment('0=FALSE, 1=TRUE');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('welcome_message');
        });

        Schema::dropIfExists('securities');
    }
}
