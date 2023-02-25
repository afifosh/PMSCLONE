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
    Schema::create('timeline_logs', function (Blueprint $table) {
      $table->id();
      $table->string('logable_type');
      $table->unsignedBigInteger('logable_id');
      $table->string('actioner_type')->nullable();
      $table->unsignedBigInteger('actioner_id')->nullable();
      $table->text('log');
      $table->json('data')->nullable();
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
    Schema::dropIfExists('timeline_logs');
  }
};
