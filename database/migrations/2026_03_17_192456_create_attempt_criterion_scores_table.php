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
        Schema::create('attempt_criterion_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_question_review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rubric_criterion_id')->constrained()->cascadeOnDelete();
            $table->decimal('points', 8, 2)->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['attempt_question_review_id', 'rubric_criterion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempt_criterion_scores');
    }
};
