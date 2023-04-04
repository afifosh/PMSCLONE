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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index()->nullable();

            $table->uuid('uuid');

            $table->foreignId('user_id')->nullable()->comment('Owner')->constrained('users');

            $table->dateTime('owner_assigned_date')->nullable();

            $table->foreignId('source_id')->nullable()->constrained('sources');

            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->string('job_title')->nullable();

            $table->string('avatar')->nullable();

            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();

            $table->unsignedBigInteger('country_id')->nullable();
            $table->foreign('country_id')
                ->references('id')
                ->on('countries');

            $table->foreignId('created_by')->nullable()->constrained('admins');

            // $table->foreignId('next_activity_id')->nullable()->constrained('activities');

            $table->softDeletes();
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
        Schema::dropIfExists('contacts');
    }
};
