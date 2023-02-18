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
        Schema::create('rfp_file_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('rfp_files')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('actioner_type');
            $table->unsignedBigInteger('actioner_id');
            $table->text('log');
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
        Schema::dropIfExists('rfp_file_logs');
    }
};
