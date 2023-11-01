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
    Schema::create('doc_signatures', function (Blueprint $table) {
      $table->id();
      $table->foreignId('uploaded_kyc_doc_id')->constrained('uploaded_kyc_docs')->cascadeOnDelete();
      $table->morphs('signer');
      $table->string('signer_position')->nullable();
      $table->timestamp('signed_at')->nullable();
      $table->boolean('is_signature')->default(true)->comment('true if signature, false if stamp');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('doc_signatures');
  }
};
