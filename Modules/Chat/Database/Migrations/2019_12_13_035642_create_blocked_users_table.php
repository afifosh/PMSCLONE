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
      // $table->unsignedInteger('blocked_by');
      $table->foreignId('blocked_by')->constrained('admins')->cascadeOnUpdate()->cascadeOnDelete();
      // $table->unsignedInteger('blocked_to');
      $table->foreignId('blocked_to')->constrained('admins')->cascadeOnUpdate()->cascadeOnDelete();
      $table->timestamps();

      // $table->foreign('blocked_by')->references('id')->on('users')
      //     ->onDelete('cascade')
      //     ->onUpdate('cascade');

      // $table->foreign('blocked_to')->references('id')->on('users')
      //     ->onDelete('cascade')
      //     ->onUpdate('cascade');
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
