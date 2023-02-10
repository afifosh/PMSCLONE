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
        Schema::create('company_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invited_person_id')->constrained('company_contact_persons')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('token');
            $table->string('role_id');
            $table->timestamp('valid_till')->useCurrent();
            $table->enum('status', ['pending', 'failed','sent', 'seen', 'accepted','revoked'])->default('sent');
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
        Schema::dropIfExists('company_invitations');
    }
};
