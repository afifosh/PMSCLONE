<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('uploaded_kyc_docs', function (Blueprint $table) {
      $table->id();
      $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete()->cascadeOnUpdate();
      // Model which is requested to upload the document
      $table->nullableMorphs('doc_requestable');
      $table->nullableMorphs('uploader');
      $table->string('refrence_id')->nullable();
      $table->foreignId('kyc_doc_id')->constrained('kyc_documents')->cascadeOnDelete()->cascadeOnUpdate();
      $table->string('expiry_date')->nullable();
      $table->json('fields')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('uploaded_kyc_docs');
  }
};
