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
    Schema::create('artwork_locations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
      $table->foreignId('location_id')->constrained()->cascadeOnDelete();
      $table->foreignId('moved_from')->nullable()->constrained('artwork_locations')->cascadeOnUpdate()->nullOnDelete(); // in case of preserving movement history, Otherwise not needed
      $table->foreignId('added_by')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
      $table->timestamp('added_till')->nullable(); // in case added for some time, null means forever
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('artwork_locations');
  }
};
