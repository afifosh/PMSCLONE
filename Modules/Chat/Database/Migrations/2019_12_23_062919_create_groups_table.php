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
    Schema::create('groups', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('name')->index();
      $table->string('description')->nullable();
      $table->string('photo_url')->nullable();
      $table->integer('privacy');
      $table->integer('group_type')->comment('1 => Open (Anyone can send message), 2 => Close (Only Admin can send message) ');
      $table->foreignId('created_by')->constrained('admins')->cascadeOnUpdate()->cascadeOnDelete();
      $table->string('project_id')->nullable();
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
    Schema::dropIfExists('groups');
  }
};
