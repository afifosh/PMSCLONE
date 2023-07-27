<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('blocked_users', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->foreignId('blocked_by')->constrained('admins')->cascadeOnUpdate()->cascadeOnDelete();
      $table->foreignId('blocked_to')->constrained('admins')->cascadeOnUpdate()->cascadeOnDelete();
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
    Schema::dropIfExists('blocked_users');
  }
};
