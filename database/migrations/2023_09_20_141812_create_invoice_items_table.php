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
      $table->string('description')->nullable();
      $table->bigInteger('subtotal')->default(0)->comment('Amount without tax');
      $table->bigInteger('total_tax_amount')->default(0);
      $table->bigInteger('total')->default(0);
      // subtotal = ($this->deduction->is_before_tax) ? $this->subtotal - $this->deduction->amount : $item->subtotal + $this->total_tax_amount;
      $table->integer('subtotal_amount_adjustment')->default(0);
      $table->integer('total_amount_adjustment')->default(0);
      $table->bigInteger('authority_inv_total')->default(0);
      $table->integer('rounding_amount')->default(0);
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
