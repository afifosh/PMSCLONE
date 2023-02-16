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
            $table->string('label', 50);
            $table->string('name', 50)->unique();
            $table->string('sent_from_address', 100)->nullable();
            $table->string('sent_from_name', 50)->nullable();
            $table->string('transport')->default('smtp');
            $table->string('host')->nullable();
            $table->smallInteger('port')->nullable();
            $table->enum('encryption', ['tls', 'ssl'])->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('access_key_id')->nullable();
            $table->string('secret_access_key')->nullable();
            $table->string('region')->nullable();
            $table->string('domain_name')->nullable();
            $table->string('api_key')->nullable();
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
