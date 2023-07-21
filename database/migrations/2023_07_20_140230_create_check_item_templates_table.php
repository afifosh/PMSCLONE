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
    Schema::create('check_item_templates', function (Blueprint $table) {
      $table->id();
      $table->foreignId('task_template_id')->constrained('task_templates')->cascadeOnDelete();
      $table->foreignId('created_by')->nullable()->constrained('admins')->cascadeOnDelete();
      $table->string('title');
      $table->boolean('status')->default(0);
      $table->integer('order')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('check_item_templates');
  }
};
