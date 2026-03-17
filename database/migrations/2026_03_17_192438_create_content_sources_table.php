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
        Schema::create('content_sources', function (Blueprint $table) {
            $table->id();
            $table->string('source_type');
            $table->string('title');
            $table->string('source_url')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('answer_source_filename')->nullable();
            $table->string('payload_hash')->nullable()->unique();
            $table->json('meta_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_sources');
    }
};
