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
    Schema::create('contract_change_requests', function (Blueprint $table) {
      $table->id();
      $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
      $table->foreignId('reviewed_by')->nullable()->constrained('admins')->nullOnDelete();
      $table->timestamp('reviewed_at')->nullable();
      $table->string('sender_type')->nullable();
      $table->string('sender_id')->nullable();
      $table->boolean('visible_to_client')->default(false);
      $table->string('reason')->nullable();
      $table->text('description')->nullable();
      $table->string('old_value')->default(0);
      $table->string('new_value')->default(0);
      $table->string('old_currency')->nullable();
      $table->string('new_currency')->nullable();
      $table->datetime('old_end_date')->nullable();
      $table->datetime('new_end_date')->nullable();
      $table->enum('type', ['Terms', 'Lifecycle'])->default('Terms');
      $table->json('data')->nullable();
      $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('contract_change_orders');
  }
};
