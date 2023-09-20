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
      $table->unsignedBigInteger('amount')->default(0);
      $table->string('description')->nullable();
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
