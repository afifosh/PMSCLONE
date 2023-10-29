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
    Schema::create('contract_phases', function (Blueprint $table) {
      $table->id();
      $table->foreignId('contract_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->foreignId('stage_id')->nullable()->constrained('contract_stages')->cascadeOnDelete()->cascadeOnUpdate();
      $table->string('name');
      $table->text('description')->nullable();
      $table->bigInteger('estimated_cost')->default(0);
      $table->bigInteger('tax_amount')->default(0);
      $table->bigInteger('adjustment_amount')->default(0);
      $table->bigInteger('total_cost')->default(0);
      $table->bigInteger('order')->default(0);
      $table->dateTime('start_date');
      $table->dateTime('due_date')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('contract_phases');
  }
};
