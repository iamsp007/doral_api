<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('file_name');
            $table->enum('type',["1","2","3","4"])->default('1')->comment('1=ID Proof,2=Degree Proof,3=medicle report,4=Insurance Report');
            $table->enum('status',['0','1'])->default('1')->comment('0=Inactive, 1=Active');
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
        Schema::dropIfExists('upload_documents');
    }
}
