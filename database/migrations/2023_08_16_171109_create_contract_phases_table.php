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
      $table->bigInteger('total_cost')->default(0);
      // subtotal = ($this->deduction->is_before_tax) ? $this->subtotal - $this->deduction->amount : $item->subtotal + $this->total_tax_amount;
      $table->integer('subtotal_amount_adjustment')->default(0);
      $table->integer('total_amount_adjustment')->default(0);
      $table->bigInteger('rounding_amount')->default(0);
      $table->bigInteger('order')->default(0);
      $table->dateTime('start_date');
      $table->dateTime('due_date')->nullable();
      $table->boolean('is_allowable_cost')->default(false);
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
