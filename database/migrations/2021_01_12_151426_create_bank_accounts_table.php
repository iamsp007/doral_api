<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index('user_id');
            $table->string('account_name');
            $table->string('account_type');
            $table->string('routing_number');
            $table->string('account_number');
            $table->longText('address_line_1')->nullable();
            $table->longText('address_line_2')->nullable();
            $table->foreignId('city')->index('city')->nullable();
            $table->foreignId('state')->index('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('send_tax_documents_to');
            $table->string('legal_entity')->nullable();
            $table->string('tax_payer_id_number')->nullable();
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
        Schema::dropIfExists('bank_accounts');
    }
}
