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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_type_id')->constrained()->restrictOnDelete();
            $table->string('external_number');
            $table->string('checking_mode')->default('auto');
            $table->longText('prompt_html');
            $table->longText('instruction_html')->nullable();
            $table->decimal('max_score', 8, 2)->default(0);
            $table->boolean('requires_manual_review')->default(false);
            $table->unsignedInteger('position')->default(1);
            $table->json('response_structure_json')->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
