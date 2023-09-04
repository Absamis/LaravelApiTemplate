<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("userid");
            $table->text("code")->nullable();
            $table->text("token")->unique();
            $table->integer("verify_type");
            $table->integer("status")->default(0);
            $table->text("data")->nullable();
            $table->foreign("userid")->references("id")->on("users")->onDelete("CASCADE");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
