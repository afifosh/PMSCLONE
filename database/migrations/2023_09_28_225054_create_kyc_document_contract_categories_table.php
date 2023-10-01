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
    Schema::create('kyc_document_contract_categories', function (Blueprint $table) {
      $table->id();
      $table->foreignId('kyc_document_id')->constrained('kyc_documents')->onDelete('cascade')->cascadeOnUpdate();
      $table->foreignId('contract_category_id')->constrained('contract_categories')->onDelete('cascade')->cascadeOnUpdate();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('kyc_document_contract_categories');
  }
};