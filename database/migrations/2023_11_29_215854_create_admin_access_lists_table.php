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
    Schema::create('admin_access_lists', function (Blueprint $table) {
      $table->id();
      $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
      $table->morphs('accessable');
      $table->timestamp('granted_till')->nullable()->comment('null means forever');
      $table->boolean('is_revoked')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('admin_access_lists');
  }
};
