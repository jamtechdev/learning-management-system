<?php

use App\Enum\QuestionTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Question Groups (Comprehension Passages) – created first
        Schema::create('question_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('passage');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 2. Questions Table – now it can safely reference question_groups
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->enum('education_type', ['primary', 'secondary']);
            $table->longText('content');
            $table->enum('type', QuestionTypes::TYPES);
            $table->text('explanation')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('parent_question_id')->nullable()->constrained('questions')->onDelete('cascade');
            $table->foreignId('level_id')->nullable()->constrained('question_levels')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('question_subjects')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('question_groups')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Question Options
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->string('label')->nullable();
            $table->text('value');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        // 4. Question Answers
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->json('answer');
            $table->string('format')->default('text');
            $table->timestamps();
        });

        // 5. Mapping Questions into Groups (Comprehension)
        Schema::create('question_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('question_groups')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->unsignedInteger('sequence')->default(0);
            $table->timestamps();
        });


    }

    public function down(): void
    {
        // Drop the pivot table first to avoid foreign key errors
        Schema::dropIfExists('question_group_items');
        Schema::dropIfExists('question_groups');
        Schema::dropIfExists('question_answers');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
    }
};
