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
        Schema::create('units', function (Blueprint $table) {
            $table->integer('unit_id', true);
            $table->dateTime('unit_created')->nullable();
            $table->dateTime('unit_update')->nullable();
            $table->integer('unit_creatorid')->nullable()->default(1);
            $table->string('unit_name', 50);
            $table->string('unit_system_default', 50)->default('no')->comment('yes|no');
            $table->string('unit_time_default', 50)->nullable()->default('no')->comment('yes|no (used to identify time unit)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
    }
};
