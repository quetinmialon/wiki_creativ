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
        Schema::create("favorites", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("document_id");
            $table->timestamps();
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("document_id")->references("id")->on("documents")->onDelete("cascade");
        });
        Schema::create("logs", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("document_id");
            $table->timestamps();
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("document_id")->references("id")->on("documents")->onDelete("cascade");
        });
        Schema::create("permissions", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("author")->nullable();
            $table->unsignedBigInteger("document_id");
            $table->string('comment')->nullable();
            $table->string('status');
            $table->unsignedBigInteger("handled_by")->nullable();
            $table->timestamp('handled_at')->nullable();
            $table->timestamp('expired_at');
            $table->timestamps();
            $table->foreign("author")->references("id")->on("users")->onDelete('set null');
            $table->foreign("document_id")->references("id")->on("documents")->onDelete('cascade');
            $table->foreign("handled_by")->references("id")->on("users")->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("favorites");
        Schema::dropIfExists("logs");
        Schema::dropIfExists("permissions");
    }
};
