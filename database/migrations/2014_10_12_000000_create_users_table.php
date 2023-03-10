<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('users', function (Blueprint $table) {
            $table->string("userid")->primary();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('username');
            $table->string('email')->unique();
            $table->string('phone', 17);
            $table->string('gender', 7)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string("photo")->nullable();
            $table->integer("status")->default(0);
            $table->integer("login_status")->default(0);
            $table->dateTime("last_login")->default(DB::raw("NOW()"));
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
};
