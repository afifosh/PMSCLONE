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
    Schema::create('contracts', function (Blueprint $table) {
      $table->id();
      $table->foreignId('type_id')->constrained('contract_types')->onDelete('cascade')->cascadeOnUpdate();
      $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade')->cascadeOnUpdate();
      $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade')->cascadeOnUpdate();
      $table->string('subject', 100);
      $table->string('value', 100);
      $table->dateTime('start_date');
      $table->dateTime('end_date')->nullable();
      $table->text('description')->nullable();
      $table->enum('status', ['Not Started', 'In Progress', 'On Hold', 'Awaiting Feedback', 'Completed'])->default('Not Started');
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('contracts');
  }
};
