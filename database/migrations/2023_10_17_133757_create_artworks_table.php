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
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->nullableMorphs('owner'); // company, partnerCompany, studio, artist, Client
            $table->year('year')->nullable();
            $table->foreignId('medium_id')->nullable()->constrained('mediums')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->cascadeOnUpdate()->nullOnDelete();
            $table->string('dimension');
            $table->text('description')->nullable(); // Add description column
            $table->string('featured_image')->nullable(); // Add featured_image column
            $table->foreignId('added_by')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};
