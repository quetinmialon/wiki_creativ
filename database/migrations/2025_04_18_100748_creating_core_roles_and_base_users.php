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
        Artisan::call('boot:all'); // This command will create necessary entries in database
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
