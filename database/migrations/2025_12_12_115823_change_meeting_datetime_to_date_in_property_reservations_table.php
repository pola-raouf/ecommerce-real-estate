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
        Schema::table('property_reservations', function (Blueprint $table) {
            // Change meeting_datetime from datetime to date
            $table->date('meeting_datetime')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_reservations', function (Blueprint $table) {
            // Revert back to datetime
            $table->dateTime('meeting_datetime')->nullable()->change();
        });
    }
};
