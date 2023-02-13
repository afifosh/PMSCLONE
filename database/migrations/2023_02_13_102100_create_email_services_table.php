<?php

use Database\Seeders\EmailServiceSeeder;
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
        Schema::create('email_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_label', 50);
            $table->string('service', 50)->unique();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        (new EmailServiceSeeder)->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_services');
    }
};
