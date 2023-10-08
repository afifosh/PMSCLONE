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
    Schema::create('kyc_doc_contracts', function (Blueprint $table) {
      $table->id();
      $table->foreignId('kyc_doc_id')->constrained('kyc_documents')->cascadeOnDelete();
      $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('kyc_doc_contracts');
  }
};
