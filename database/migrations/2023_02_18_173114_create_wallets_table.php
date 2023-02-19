<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string("userid")->nullable();
            $table->double("balance")->default(0);
            $table->double("blocked_balance")->default(0);
            $table->string("wallet_name")->nullable();
            $table->string("wallet_number", 11)->nullable();
            $table->string("wallet_bank_name", 150)->nullable();
            $table->string("wallet_bank_code", 7)->nullable();
            $table->integer("status")->default(1);
            $table->foreign("userid")->references("userid")->on("users")->cascadeOnDelete();
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
        Schema::dropIfExists('wallets');
    }
};
