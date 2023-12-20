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
    Schema::create('invoice_payments', function (Blueprint $table) {
      $table->id();
      $table->morphs('payable');
      $table->foreignId('ba_trx_id')->constrained('account_balance_transactions')->onDelete('cascade');
      $table->string('transaction_id')->nullable();
      $table->date('payment_date');
      $table->bigInteger('amount')->default(0);
      $table->text('note')->nullable();
      $table->integer('type')->default(0)->comment('0: invoice payment, 1: retention payment, 2: wht payment, 3: rc payment, 4: both wht and rc payment');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('invoice_payments');
  }
};
