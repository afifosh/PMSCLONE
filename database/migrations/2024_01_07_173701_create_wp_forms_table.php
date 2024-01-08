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
        Schema::create('wp_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('field_id')->default(0); // Assuming this is how you want to track field IDs
            $table->json('fields'); // Storing all fields as JSON
            $table->json('settings'); // Storing all settings as JSON
            $table->json('meta')->nullable(); // Storing meta as JSON
            $table->string('status')->default(0);
            $table->timestamps();
            // Add other columns as necessary
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wp_forms');
    }
};
