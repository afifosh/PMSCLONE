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
        'Start Date Revised',
        'End Date Revised',
        'Rescheduled',
        'Terminated',
        'Undo Terminate',
        'Amount Increased',
        'Amount Decreased',
        'Rescheduled And Amount Increased',
        'Rescheduled And Amount Decreased',
        'Start Date Revised And Amount Increased',
        'Start Date Revised And Amount Decreased',
        'End Date Revised And Amount Increased',
        'End Date Revised And Amount Decreased',
      ]);
      $table->json('modifications')->nullable();
      $table->text('description')->nullable();
      $table->dateTime('applied_at')->nullable(); // are the future events applied or not, if applied then when. like scheduled termination, scheduled resume,
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
