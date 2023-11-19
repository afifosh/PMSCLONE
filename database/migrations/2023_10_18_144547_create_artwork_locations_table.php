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
    Schema::dropIfExists('artwork_locations');

    Schema::create('artwork_locations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
      $table->foreignId('location_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
      $table->foreignId('warehouse_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
      $table->foreignId('contract_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
      $table->foreignId('added_by')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
      $table->timestamp('datein')->nullable(); // in case added for some time, null means forever
      $table->timestamp('dateout')->nullable(); // in case added for some time, null means forever 
      $table->text('remarks')->nullable();
      $table->boolean('is_current')->default(false);
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
