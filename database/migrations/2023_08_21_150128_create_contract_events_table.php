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
    Schema::create('contract_events', function (Blueprint $table) {
      $table->id();

      $table->foreignId('contract_id')->constrained()->onDelete('cascade');
      $table->foreignId('admin_id')->nullable()->constrained()->onDelete('cascade');
      $table->enum('event_type', [
        'Created',
        'Paused',
        'Resumed',
        'Extended',
        'Shortened',
        'Rescheduled',
        'Terminated',
        'Amount Increased',
        'Amount Decreased',
        'Rescheduled And Amount Increased',
        'Rescheduled And Amount Decreased'
      ]);
      $table->json('modifications')->nullable();
      $table->text('description')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('contract_events');
  }
};