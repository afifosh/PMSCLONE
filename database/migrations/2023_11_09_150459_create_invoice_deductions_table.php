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
    Schema::create('invoice_deductions', function (Blueprint $table) {
      $table->id();
      $table->morphs('deductible');
      $table->foreignId('downpayment_id')->nullable()->constrained('invoices');
      $table->foreignId('dp_rate_id')->nullable()->constrained('invoice_configs');
      $table->boolean('is_percentage')->default(false);
      $table->bigInteger('amount')->default(0);
      $table->bigInteger('manual_amount')->default(0);
      $table->integer('percentage')->default(0);
      $table->boolean('is_before_tax')->default(false);
      $table->enum('calculation_source', ['Deductible', 'Down Payment'])->default('Deductible');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('invoice_deductions');
  }
};
