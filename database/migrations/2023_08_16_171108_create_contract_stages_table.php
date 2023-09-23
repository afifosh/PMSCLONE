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
    Schema::create('contract_stages', function (Blueprint $table) {
      $table->id();
      $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
      $table->string('name')->nullable();
      $table->date('start_date')->nullable();
      $table->date('due_date')->nullable();
      $table->bigInteger('estimated_cost')->default(0);
      $table->text('description')->nullable();
      $table->enum('status', ['Active', 'Completed', 'Cancelled'])->default('Active');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('contract_stages');
  }
};
