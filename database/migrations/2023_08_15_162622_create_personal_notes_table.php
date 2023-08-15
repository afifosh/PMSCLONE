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
    Schema::create('personal_notes', function (Blueprint $table) {
      $table->id();
      $table->foreignId('note_tag_id')->constrained()->cascadeOnDelete();
      $table->string('user_type');
      $table->string('user_id');
      $table->string('title');
      $table->text('description');
      $table->boolean('is_favorite')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('personal_notes');
  }
};
