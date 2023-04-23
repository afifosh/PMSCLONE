<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_has_custom_field_options', function (Blueprint $table) {
            $table->unsignedBigInteger('model_id');
            $table->string('model_type');
            $table->index(['model_id', 'model_type']);
            $table->foreignId('custom_field_id')->constrained('custom_fields')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('custom_field_options')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_has_custom_field_options');
    }
};
