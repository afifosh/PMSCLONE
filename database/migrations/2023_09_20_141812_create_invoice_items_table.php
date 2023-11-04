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
    Schema::create('invoice_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
      $table->nullableMorphs('invoiceable');
      $table->unsignedBigInteger('amount')->default(0)->comment('Amount without tax');
      $table->unsignedBigInteger('total_tax_amount')->default(0);
      $table->unsignedBigInteger('manual_tax_amount')->default(0);
      // These fields are for inline downpayment.
      $table->foreignId('downpayment_id')->nullable()->constrained('invoices')->cascadeOnDelete();
      $table->boolean('is_downpayment_percentage')->default(false);
      $table->unsignedInteger('downpayment_percentage')->default(0);
      $table->unsignedBigInteger('downpayment_amount')->default(0);
      $table->unsignedBigInteger('manual_downpayment_amount')->default(0);
      // End of downpayment fields.
      $table->string('description')->nullable();
      $table->unsignedInteger('order')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('invoice_items');
  }
};
