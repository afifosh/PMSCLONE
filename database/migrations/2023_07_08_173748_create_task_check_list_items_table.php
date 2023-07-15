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
    Schema::create('task_check_list_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
      $table->foreignId('assigned_to')->nullable()->constrained('admins')->cascadeOnDelete();
      $table->dateTime('due_date')->nullable();
      $table->foreignId('created_by')->nullable()->constrained('admins')->cascadeOnDelete();
      $table->foreignId('completed_by')->nullable()->constrained('admins')->cascadeOnDelete();
      $table->string('title');
      $table->boolean('status')->default(0);
      $table->integer('order')->default(0);
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('task_check_list_items');
  }
};
