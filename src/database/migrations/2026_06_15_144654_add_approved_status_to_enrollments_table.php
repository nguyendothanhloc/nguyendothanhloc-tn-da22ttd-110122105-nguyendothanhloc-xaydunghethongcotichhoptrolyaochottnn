<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify ENUM to add 'approved' status
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending', 'paid', 'approved', 'cancelled') NOT NULL DEFAULT 'pending'");
        
        // Update existing 'paid' status to 'approved' for better semantics
        DB::table('enrollments')
            ->where('status', 'paid')
            ->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update 'approved' back to 'paid' before removing from ENUM
        DB::table('enrollments')
            ->where('status', 'approved')
            ->update(['status' => 'paid']);
            
        // Revert ENUM to original values
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending', 'paid', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
