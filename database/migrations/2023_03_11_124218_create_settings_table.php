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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->nullable();
            $table->longText('value')->nullable();
            $table->string('settingable_type', 160)->nullable();
            $table->unsignedBigInteger('settingable_id')->nullable();
            $table->string('context')->nullable();
            $table->boolean('autoload')->default(0);
            $table->boolean('public')->default(1);
            $table->timestamps();

            $table->index(['settingable_type', 'settingable_id'], 'settingable_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
