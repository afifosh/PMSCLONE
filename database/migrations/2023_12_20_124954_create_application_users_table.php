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
    Schema::create('application_users', function (Blueprint $table) {
      $table->id();
      $table->foreignId('application_id')->constrained()->cascadeOnDelete();
      $table->foreignId('admin_id')->constrained()->cascadeOnDelete();
      $table->integer('role');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('application_users');
  }
};
