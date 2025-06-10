<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_topics', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->foreignIdFor(\App\Models\QuestionLevel::class, 'level_id')->nullable()->constrained('question_levels')->onDelete('cascade');
            $table->foreignIdFor(\App\Models\QuestionSubject::class, 'subject_id')->nullable()->constrained('question_subjects')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\QuestionTopic::class, 'topic_id')->nullable()->constrained('question_topics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_topics');

        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->dropColumn('topic_id');
        });
    }
};
