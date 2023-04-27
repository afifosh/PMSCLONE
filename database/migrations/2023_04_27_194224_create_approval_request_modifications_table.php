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
        Schema::create('approval_request_modifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_request_id')->constrained('company_approval_requests')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('modification_id');
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
        Schema::dropIfExists('approval_request_modifications');
    }
};
