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
    Schema::dropIfExists('invoice_taxes');
  }
};
