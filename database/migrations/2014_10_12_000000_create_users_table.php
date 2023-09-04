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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete()->cascadeOnUpdate();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('job_title')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->default('Male');
            $table->string('password')->nullable();
            $table->string('address')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('language')->default('en');
            $table->string('timezone')->nullable();
            $table->string('currency')->nullable();
            $table->rememberToken();
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->boolean('is_primary')->default(0);
            $table->boolean('can_login')->default(0);
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
        Schema::dropIfExists('users');
    }
};
