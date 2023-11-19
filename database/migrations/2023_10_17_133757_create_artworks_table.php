<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\LengthUnit;
use App\Enums\WeightUnit;

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
            $table->text('description')->nullable(); // Add description column
            $table->string('featured_image')->nullable(); // Add featured_image column
            $table->decimal('weight', 8, 2)->default(0);
            $table->string('weight_unit')->default(WeightUnit::KILOGRAM->value);
            $table->decimal('width', 8, 2)->default(0);
            $table->string('width_unit')->default(LengthUnit::CENTIMETER->value);
            $table->decimal('height', 8, 2)->default(0);
            $table->string('height_unit')->default(LengthUnit::CENTIMETER->value);
            $table->decimal('depth', 8, 2)->default(0);
            $table->string('depth_unit')->default(LengthUnit::CENTIMETER->value);
            $table->foreignId('added_by')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('is_sold')->default(false);
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
