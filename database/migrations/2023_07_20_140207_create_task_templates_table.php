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
    Schema::create('task_templates', function (Blueprint $table) {
      $table->id();
      $table->foreignId('project_template_id')->constrained('project_templates')->cascadeOnDelete();
      $table->string('subject')->nullable();
      $table->text('description')->nullable();
      $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->default('Low');
      $table->boolean('is_completed_checklist_hidden')->default(false);
      $table->json('tags')->nullable();
      $table->enum('status', ['Not Started', 'In Progress', 'On Hold', 'Awaiting Feedback', 'Completed'])->default('Not Started');
      $table->bigInteger('order')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('task_templates');
  }
};
