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
    Schema::create('custom_invoice_items', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->unsignedBigInteger('price')->default(0);
      $table->unsignedBigInteger('quantity')->default(1);
      $table->unsignedBigInteger('subtotal')->default(0);
      // // downpayment
      // $table->foreignId('downpayment_id')->nullable()->constrained('invoices')->cascadeOnDelete();
      // $table->unsignedBigInteger('downpayment_amount')->default(0);
      // //tax
      // $table->bigInteger('total_tax_amount')->default(0);
      // $table->bigInteger('manual_tax_amount')->default(0);
      // //end tax
      // $table->unsignedBigInteger('total')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('custom_invoice_items');
  }
};
