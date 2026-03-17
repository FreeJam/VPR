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
        Schema::create('assessment_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('grading_scale_id')->nullable();
            $table->string('version_label')->default('v1');
            $table->string('status')->default('draft');
            $table->boolean('is_current')->default(true);
            $table->timestamp('imported_at')->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->unique(['assessment_id', 'version_label']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_versions');
    }
};
