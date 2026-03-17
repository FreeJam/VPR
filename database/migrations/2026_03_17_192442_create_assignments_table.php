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
            $table->foreignId('teacher_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assessment_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('instructions')->nullable();
            $table->string('mode')->default('training');
            $table->string('status')->default('draft');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->unsignedInteger('max_attempts')->default(1);
            $table->boolean('is_published')->default(false);
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index(['teacher_profile_id', 'due_at']);
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
