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
    Schema::create('application_submitters', function (Blueprint $table) {
      $table->id();
      $table->foreignId('application_id')->constrained()->cascadeOnDelete();
      $table->morphs('submitter');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('application_submitters');
  }
};
