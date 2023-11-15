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
    Schema::create('authority_invoices', function (Blueprint $table) {
      $table->id();
      $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
      $table->boolean('is_summary_tax')->default(false);
      $table->bigInteger('subtotal')->default(0);
      $table->bigInteger('total_tax')->default(0);
      $table->bigInteger('total')->default(0);
      $table->integer('rounding_amount')->default(0);
      $table->bigInteger('paid_amount')->default(0);
      /**
       * Downpayment
       */
      $table->unsignedBigInteger('downpayment_amount')->default(0);
      /**
       * Discount
       */
      $table->enum('discount_type', ['Fixed', 'Percentage'])->nullable();
      $table->integer('discount_percentage')->default(0);
      $table->bigInteger('discount_amount')->default(0);
      /**
       * Adjustment
       */
      $table->string('adjustment_description')->nullable();
      $table->bigInteger('adjustment_amount')->default(0);
      /**
       * Retention
       */
      $table->foreignId('retention_id')->nullable()->constrained('invoice_configs')->nullOnDelete();
      $table->string('retention_name')->nullable();
      $table->integer('retention_percentage')->default(0);
      $table->bigInteger('retention_amount')->default(0);
      $table->timestamp('retention_released_at')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('authority_invoices');
  }
};
