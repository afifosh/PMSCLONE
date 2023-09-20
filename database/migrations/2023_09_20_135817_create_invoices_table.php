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
    Schema::create('invoices', function (Blueprint $table) {
      $table->id();
      $table->foreignId('company_id')->constrained()->cascadeOnDelete();
      $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
      $table->date('invoice_date');
      $table->date('due_date');
      $table->date('sent_date')->nullable()->comment('Date the invoice was sent to the client');
      $table->nullableMorphs('creator');
      $table->bigInteger('subtotal')->default(0);
      $table->text('note')->nullable();
      $table->text('terms')->nullable();
      $table->enum('status', ['Draft', 'Sent', 'Paid', 'Partial paid','Cancelled'])->default('draft');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('invoices');
  }
};
