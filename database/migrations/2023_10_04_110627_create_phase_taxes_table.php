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
    Schema::create('phase_taxes', function (Blueprint $table) {
      $table->id();
      $table->foreignId('contract_phase_id')->constrained('contract_phases')->cascadeOnDelete();
      $table->foreignId('tax_id')->constrained()->cascadeOnDelete();
      $table->unsignedBigInteger('amount')->default(0);
      $table->enum('type', ['Percent', 'Fixed'])->default('Percent');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('phase_taxes');
  }
};
