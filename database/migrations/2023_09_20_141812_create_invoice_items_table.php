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
      $table->bigInteger('subtotal')->default(0)->comment('Amount without tax');
      $table->bigInteger('total_tax_amount')->default(0);
      $table->bigInteger('manual_tax_amount')->default(0);
      $table->bigInteger('total')->default(0);
      $table->integer('rounding_amount')->default(0);
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
