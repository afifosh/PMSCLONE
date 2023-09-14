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
    Schema::create('financial_years', function (Blueprint $table) {
      $table->id();
      $table->string('label')->nullable();
      $table->date('start_date');
      $table->date('end_date');
      $table->bigInteger('initial_balance')->default(0); // This is the initial balance
      $table->bigInteger('balance')->default(0); // This is the current balance
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('financial_years');
  }
};
