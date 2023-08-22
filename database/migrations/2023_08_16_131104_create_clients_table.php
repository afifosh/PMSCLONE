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
    Schema::create('clients', function (Blueprint $table) {
      $table->id();
      $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete()->cascadeOnUpdate();
      $table->string('first_name')->nullable();
      $table->string('last_name')->nullable();
      $table->string('phone')->nullable();
      $table->string('email')->unique();
      $table->string('avatar')->nullable();
      $table->string('address')->nullable();
      $table->string('state')->nullable();
      $table->string('zip_code')->nullable();
      $table->string('language')->nullable();
      $table->string('timezone')->nullable();
      $table->string('currency')->nullable();
      $table->enum('status', ['Active', 'Suspended'])->default('Active');
      $table->timestamp('last_seen')->nullable();
      $table->tinyInteger('is_online')->default(0)->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('clients');
  }
};
