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
        Schema::create('items', function (Blueprint $table) {
            $table->integer('item_id', true);
            $table->dateTime('item_created')->nullable();
            $table->dateTime('item_updated')->nullable();
            $table->integer('item_categoryid')->default(8)->index('item_categoryid');
            $table->integer('item_creatorid');
            $table->string('item_type', 100)->default('standard')->comment('standard|dimensions');
            $table->text('item_description')->nullable();
            $table->string('item_unit', 50)->nullable();
            $table->decimal('item_rate', 10);
            $table->string('item_tax_status', 100)->default('taxable')->comment('taxable|exempt');
            $table->decimal('item_dimensions_length', 10)->nullable();
            $table->decimal('item_dimensions_width', 10)->nullable();
            $table->text('item_notes_estimatation')->nullable();
            $table->text('item_notes_production')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
};
