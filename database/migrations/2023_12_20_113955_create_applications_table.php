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
    Schema::create('applications', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->foreignId('program_id')->constrained()->cascadeOnDelete();
      $table->foreignId('type_id')->constrained('application_types')->cascadeOnDelete();
      $table->foreignId('category_id')->constrained('application_categories')->cascadeOnDelete();
      $table->foreignId('pipeline_id')->constrained('application_pipelines')->cascadeOnDelete();
      $table->foreignId('scorecard_id')->constrained('application_score_cards')->cascadeOnDelete();
      $table->string('form_id')->nullable();
      $table->string('description')->nullable();
      $table->timestamp('start_at')->nullable();
      $table->timestamp('end_at')->nullable();
      $table->boolean('is_public')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('applications');
  }
};
