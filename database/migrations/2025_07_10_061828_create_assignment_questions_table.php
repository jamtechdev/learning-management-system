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
        Schema::create('assignment_questions', function (Blueprint $table) {
            $table->id();
            // Explicitly specify the referenced table
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            // Explicitly specify the referenced table for the 'question_id' foreign key
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_questions');
    }
};
