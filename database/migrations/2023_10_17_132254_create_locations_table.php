<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->foreignId('country_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('state_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7); // Adjust precision and scale as needed
            $table->decimal('longitude', 10, 7); // Adjust precision and scale as needed
            $table->integer('zoomLevel')->default(0);
            $table->foreignId('added_by')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_warehouse')->default(false);
            $table->nullableMorphs('owner');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
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
        Schema::dropIfExists('locations');
    }
}
