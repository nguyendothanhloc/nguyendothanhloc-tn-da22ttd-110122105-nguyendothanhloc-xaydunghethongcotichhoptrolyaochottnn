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
        Schema::table('classes', function (Blueprint $table) {
            $table->enum('shift', ['morning', 'afternoon', 'evening'])->nullable()->after('status');
            $table->string('weekdays')->nullable()->after('shift')->comment('Comma-separated weekdays: 2,4,6 for Mon,Wed,Fri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['shift', 'weekdays']);
        });
    }
};
