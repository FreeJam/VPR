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
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('source_type')->default('json');
            $table->string('original_filename');
            $table->string('stored_path');
            $table->string('format_version')->default('1.0');
            $table->string('status')->default('uploaded');
            $table->string('payload_hash')->nullable()->unique();
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('imported_items')->default(0);
            $table->unsignedInteger('error_count')->default(0);
            $table->json('preview_json')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
