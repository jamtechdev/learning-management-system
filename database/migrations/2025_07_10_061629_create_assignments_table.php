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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_date');
            $table->boolean('is_recurring')->default(false);
            $table->json('recurrence_rule')->nullable(); // Store recurring rules like "every Monday"
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade'); // Foreign key to User (Student)
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Foreign key to User (Parent who created the assignment)
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
