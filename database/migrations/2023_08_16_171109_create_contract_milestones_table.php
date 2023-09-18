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
    Schema::create('contract_milestones', function (Blueprint $table) {
      $table->id();
      $table->foreignId('phase_id')->constrained('contract_phases')->cascadeOnDelete()->cascadeOnUpdate();
      $table->string('name');
      $table->text('description')->nullable();
      $table->bigInteger('estimated_cost')->default(0);
      $table->bigInteger('order')->default(0);
      $table->dateTime('start_date');
      $table->dateTime('due_date');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('contract_milestones');
  }
};
