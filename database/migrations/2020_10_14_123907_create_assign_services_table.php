<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->index('patient_id');
            $table->foreignId('assign_to')->index('assign_to');
            $table->foreignId('assign_from')->index('assign_from');
            $table->text('comment');
            $table->dateTime('assign_datetime');
            $table->enum('priority', ['high', 'low', 'mediam']);
            $table->tinyInteger('is_attached');
            $table->string('document_name');
            $table->string('document_details');
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
        Schema::dropIfExists('assign_services');
    }
}
