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
      $table->timestamp('required_at')->nullable();
      $table->enum('required_at_type', ['Before', 'After', 'On'])->default('Before');
      $table->integer('signatures_required')->default(0);
      $table->integer('stamps_required')->default(0);
      $table->boolean('having_refrence_id')->default(false);
      $table->boolean('is_global')->default(false)->comment('global will be shared between all invoices of same contract');
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
