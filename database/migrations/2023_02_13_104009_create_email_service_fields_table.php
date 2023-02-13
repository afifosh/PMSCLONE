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
        Schema::create('email_service_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_service_id')->constrained('email_services')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('field_name', 50);
            $table->string('field_value', 100);
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
        Schema::dropIfExists('email_service_fields');
    }
};
