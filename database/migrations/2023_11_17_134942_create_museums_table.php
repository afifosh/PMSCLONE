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
        Schema::create('museums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('added_by')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete()->cascadeOnUpdate();
            $table->string('name')->unique();
            $table->string('website')->unique()->nullable();
            $table->string('avatar')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('phone')->nullable();
            $table->text('biography')->nullable();
            $table->string('language')->nullable();  // Add language column
            $table->string('timezone')->nullable(); // Add timezone column
            $table->string('currency')->nullable(); // Add currency column            
            $table->enum('status', ['active', 'disabled', 'pending']);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('museums');
    }
};
