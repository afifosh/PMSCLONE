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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('added_by')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete()->cascadeOnUpdate();
            $table->string('name')->unique()->nullable();
            $table->string('name_ar')->unique()->nullable();
            $table->string('website')->unique()->nullable();
            $table->string('avatar')->nullable();
            $table->string('email')->nullable();

            $table->enum('type', ['Company', 'Person']); // Add enum column

            $table->string('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('phone')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('gst_number')->nullable();

            $table->enum('status', ['active', 'disabled', 'pending']);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->integer('approval_status')->default(0)->comment('0:pending info, 1:approved, 2: ready for Approval, 3: need to be updated');
            $table->integer('approval_level')->default(0);
            $table->enum('source', ['Self Enrolled', 'Self Registered'])->default('Self Enrolled');
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('companies');
    }
};
