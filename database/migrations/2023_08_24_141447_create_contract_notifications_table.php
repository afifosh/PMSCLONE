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
    // contract expiry notifications, record expiry notifications, contract expiry reminders
    Schema::create('contract_notifications', function (Blueprint $table) {
      $table->id();
      $table->foreignId('contract_id')->constrained()->onDelete('cascade');
      $table->foreignId('sent_to')->constrained('admins')->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('contract_notifications');
  }
};
