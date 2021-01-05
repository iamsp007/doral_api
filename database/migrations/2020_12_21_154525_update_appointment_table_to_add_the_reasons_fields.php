<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppointmentTableToAddTheReasonsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('reason_id')->index('reason_id')->after('appointment_url')->nullable();
            $table->longText('reason_notes')->after('reason_id')->nullable();
            $table->enum('cancel_user', [1, 2, 3])->comment('1 => patient_id, 2 => provider1, 3 => provider2')->after('reason_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('reason_id');
            $table->dropColumn('reason_notes');
            $table->dropColumn('cancel_user');
        });
    }
}
