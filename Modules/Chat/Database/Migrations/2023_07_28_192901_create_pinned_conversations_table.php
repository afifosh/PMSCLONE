<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('pinned_conversations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('pinned_by')->constrained('admins')->onDelete('cascade');
      $table->string('conversation_id');
      $table->string('conversation_type');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('pinned_conversations');
  }
};
