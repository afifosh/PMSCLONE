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
      $table->foreignId('type_id')->nullable()->constrained('contract_types')->onDelete('cascade')->cascadeOnUpdate();
      $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade')->cascadeOnUpdate();
      $table->string('assignable_type')->nullable();
      $table->unsignedBigInteger('assignable_id')->nullable();
      $table->string('refrence_id', 100)->unique()->nullable();
      $table->string('subject', 100);
      $table->bigInteger('value')->default(0);
      $table->string('currency', 100)->default('USD');
      $table->dateTime('start_date')->nullable();
      $table->dateTime('end_date')->nullable();
      $table->text('description')->nullable();
      $table->enum('status', ['Active', 'Paused', 'Terminated', 'Draft'])->default('Active'); // active status will be used for further calculations(upcoming, expired, about to expire)
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
