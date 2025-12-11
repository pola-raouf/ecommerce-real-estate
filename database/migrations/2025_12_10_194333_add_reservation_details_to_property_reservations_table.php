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
            // Meeting date/time for property viewing (required for both rent and sale)
            $table->dateTime('meeting_datetime')->nullable();
            
            // Rental-specific fields (only for rent properties)
            $table->date('start_date')->nullable(); // When rental period starts
            $table->integer('duration_value')->nullable(); // Duration number (e.g., 6)
            $table->string('duration_unit', 20)->nullable(); // weeks, months, years
            
            // Optional notes from user
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_reservations', function (Blueprint $table) {
            $table->dropColumn([
                'meeting_datetime',
                'start_date',
                'duration_value',
                'duration_unit',
                'notes'
            ]);
        });
    }
};
