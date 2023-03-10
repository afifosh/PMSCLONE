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
        Schema::create('draft_data', function (Blueprint $table) {
            $table->id();
            $table->string('draftable_type');
            $table->unsignedBigInteger('draftable_id');
            $table->string('type')->nullable();
            $table->json('data');
            $table->index(['draftable_type', 'draftable_id']);
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
        Schema::dropIfExists('draft_data');
    }
};
