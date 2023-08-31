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
        Schema::create('contract_phases', function (Blueprint $table) {
          $table->id();
          $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete()->cascadeOnUpdate();
          $table->string('name');
          $table->text('description')->nullable();
          $table->bigInteger('estimated_cost')->default(0);
          $table->bigInteger('order')->default(0);
          $table->dateTime('start_date');
          $table->dateTime('due_date');
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_phases');
    }
};
