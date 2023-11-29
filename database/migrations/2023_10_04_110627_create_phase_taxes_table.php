<?php

use App\Models\InvoiceConfig;
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
      $table->foreignId('tax_id')->constrained('invoice_configs')->cascadeOnDelete();
      $table->bigInteger('amount')->default(0);
      $table->enum('type', ['Percent', 'Fixed'])->default('Percent');
      $table->bigInteger('calculated_amount')->default(0);
      $table->bigInteger('manual_amount')->default(0);
      $table->integer('category')->default(1)->comment('1: Value Added Tax, 2: Withholding Tax, 3: Reverse Charge Tax');
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
