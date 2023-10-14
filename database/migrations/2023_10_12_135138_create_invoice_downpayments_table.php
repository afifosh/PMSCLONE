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
    Schema::create('invoice_downpayments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
      $table->foreignId('downpayment_id')->constrained('invoices')->cascadeOnDelete();
      $table->boolean('is_percentage')->default(false);
      $table->unsignedBigInteger('amount')->default(0);
      $table->unsignedInteger('percentage')->default(0);
      $table->boolean('is_after_tax')->default(true);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('invoice_downpayments');
  }
};
