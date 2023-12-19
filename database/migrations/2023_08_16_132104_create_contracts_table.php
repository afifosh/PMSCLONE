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
      $table->foreignId('category_id')->nullable()->constrained('contract_categories')->onDelete('cascade')->cascadeOnUpdate();
      $table->foreignId('type_id')->nullable()->constrained('contract_types')->onDelete('cascade')->cascadeOnUpdate();
      $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade')->cascadeOnUpdate();
      $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('cascade')->cascadeOnUpdate();
      $table->nullableMorphs('assignable');
      $table->boolean('visible_to_client')->default(false);
      $table->string('refrence_id', 100)->nullable();
      $table->string('subject');
      $table->string('currency', 5)->default('SAR');
      $table->unsignedBigInteger('value')->default(0);
      $table->bigInteger('tax_value')->default(0);
      $table->enum('invoicing_method', ['Recuring', 'Phase Based'])->default('Phase Based');
      $table->dateTime('start_date')->nullable();
      $table->dateTime('end_date')->nullable();
      $table->dateTime('signature_date')->nullable();
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
