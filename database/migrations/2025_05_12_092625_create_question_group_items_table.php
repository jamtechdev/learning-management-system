<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Questions Table
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                'fill_blank',
                'spelling',
                'correct_sequence',
                'linking',
                'true_false',
                'mcq',
                'math',
                'grouped',
                'comprehension'
            ]);
            $table->text('content');
            $table->text('explanation')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('parent_question_id')->nullable()->constrained('questions')->onDelete('cascade');
            $table->timestamps();
        });

        // Question Options Table
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->string('label')->nullable(); // A1, B1 etc. for linking
            $table->text('value');
            $table->boolean('is_correct')->default(false);
            $table->json('metadata')->nullable(); // UI-specific (image URL, latex, etc.)
            $table->timestamps();
        });

        // Question Answers Table
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->json('answer'); // Could be text, array, mapping
            $table->string('format')->default('text'); // text, ordered, fraction, mapping
            $table->timestamps();
        });

        // Question Groups (Comprehension Passages)
        Schema::create('question_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('passage');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Mapping questions into groups (comprehension)
        Schema::create('question_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('question_groups')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->unsignedInteger('sequence')->default(0);
            $table->timestamps();
        });

        // Tagging Questions by subject, topic, etc.
        Schema::create('question_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->enum('tag_type', ['subject', 'level', 'term', 'topic', 'type']);
            $table->string('value');
            $table->timestamps();
        });

        // Optional difficulty table (if used separately from tags)
        Schema::create('question_difficulties', function (Blueprint $table) {
            $table->id();
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->timestamps();
        });

        // Pivot table for question <-> tag (many-to-many tagging system)
        Schema::create('question_question_tag', function (Blueprint $table) {
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('question_tag_id')->constrained('question_tags')->onDelete('cascade');
            $table->primary(['question_id', 'question_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_question_tag');
        Schema::dropIfExists('question_difficulties');
        Schema::dropIfExists('question_tags');
        Schema::dropIfExists('question_group_items');
        Schema::dropIfExists('question_groups');
        Schema::dropIfExists('question_answers');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
    }
};
