<?php

use App\Models\Invoice;
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
    Schema::create('kyc_documents', function (Blueprint $table) {
      $table->id();
      $table->enum('workflow', ['Company Kyc', 'Contract Required Docs', 'Invoice Required Docs'])->default('Company Kyc');
      $table->enum('client_type', ['Person', 'Company', 'Both'])->default('Both');
      $table->enum('invoice_type', Invoice::TYPES)->nullable(); // for invoice required docs only, null for both types
      $table->string('title');
      $table->string('required_from');
      $table->boolean('status');
      $table->boolean('is_mendatory')->default(false);
      $table->text('description')->nullable();
      $table->boolean('is_expirable')->default(false);
      $table->string('expiry_date_title')->nullable();
      $table->boolean('is_expiry_date_required')->default(false);
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
    Schema::dropIfExists('kyc_documents');
  }
};
