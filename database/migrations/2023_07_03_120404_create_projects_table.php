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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('project_categories')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_progress_calculatable')->default(false); // calculate progress based on tasks
            $table->integer('progress')->default(0); // 0-100
            $table->integer('status')->default(0); // 0: not started, 1: in progress, 2: on hold, 3: cancelled, 4: completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
