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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->json('allowed_prompts');
            $table->integer('max_generations_per_month')
                ->nullable();
            $table->boolean('allow_file_transcription')
                ->default(false);
            $table->json('features')
                ->nullable();
            $table->boolean('is_active')
                ->default(true);
            $table->integer('sort_order')
                ->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
