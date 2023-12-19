<?php

use App\Models\AuthorityInvoice;
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
      $table->bigInteger('total_wht')->default(0);
      $table->bigInteger('total_rc')->default(0);
      $table->bigInteger('total')->default(0);
      $table->integer('rounding_amount')->default(0);
      $table->integer('paid_wht_amount')->default(0);
      $table->integer('paid_rc_amount')->default(0);
      $table->date('due_date');
      $table->enum('status', AuthorityInvoice::STATUSES)->default('Draft');
      $table->softDeletes();
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
