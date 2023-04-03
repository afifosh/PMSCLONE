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
            $table->string('name')->unique();
            $table->string('website')->unique();
            $table->string('avatar')->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['active', 'disabled', 'pending']);
            $table->timestamp('approved_at')->nullable();
            $table->integer('approval_status')->default(0)->comment('0:pending info, 1:approved, 2: ready for Approval, 3: need to be updated');
            $table->integer('approval_level')->default(0);
            $table->enum('source', ['Self Enrolled', 'Self Registered'])->default('Self Enrolled');
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
