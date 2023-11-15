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
    Schema::create('invoice_taxes', function (Blueprint $table) {
      $table->id();
      $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
      $table->foreignId('invoice_item_id')->nullable()->constrained('invoice_items')->cascadeOnDelete();
      $table->foreignId('tax_id')->constrained('invoice_configs')->cascadeOnDelete();
      $table->bigInteger('amount')->default(0)->comment('tax rate or fixed amount');
      $table->bigInteger('calculated_amount')->default(0)->comment('tax amount to be applied');
      $table->bigInteger('manual_amount')->default(0)->comment('manual tax amount to be applied');
      $table->boolean('is_simple_tax')->default(true);
      $table->boolean('pay_on_behalf')->default(false);
      $table->boolean('is_authority_tax')->default(false);
      $table->enum('type', ['Percent', 'Fixed'])->default('Percent');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('invoice_taxes');
  }
};
