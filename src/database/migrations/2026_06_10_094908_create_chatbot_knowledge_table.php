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
        Schema::create('chatbot_knowledge', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100);
            $table->text('question');
            $table->text('answer');
            $table->string('keywords', 500)->nullable();
            $table->integer('priority')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Standard indexes
            $table->index('is_active');
            $table->index('category');
            $table->index('priority');
            
            // Composite index for optimized queries
            $table->index(['category', 'is_active', 'priority']);
        });
        
        // FULLTEXT indexes for efficient text search (MySQL only)
        // SQLite doesn't support FULLTEXT indexes, but the query will still work with LIKE
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('chatbot_knowledge', function (Blueprint $table) {
                $table->fullText(['question', 'keywords']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_knowledge');
    }
};
