<?php

use App\Models\InvoiceConfig;
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
    Schema::create('invoice_configs', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->enum('type', ['Percent', 'Fixed'])->default('Percent');
      $table->BigInteger('amount')->default(0);
      $table->enum('status', ['Active', 'Inactive'])->default('Active');
      $table->enum('config_type', ['Tax', 'Retention', 'Down Payment'])->default('Tax');
      $table->integer('category')->default(1)->comment('1: Value Added Tax, 2: Withholding Tax, 3: Reverse Charge Tax');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('taxes');
  }
};
