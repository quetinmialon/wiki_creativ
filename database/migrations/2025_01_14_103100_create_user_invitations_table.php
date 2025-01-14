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
        Schema::create('user_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->string('email');
            $table->timestamps();
        });

        Schema::create('user_invitation_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_invitation_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            $table->foreign('user_invitation_id')->references('id')->on('user_invitations')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_invitations');
        Schema::dropIfExists('user_invitation_role');
    }
};
