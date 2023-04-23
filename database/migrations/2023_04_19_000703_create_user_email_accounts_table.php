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
        Schema::create('user_email_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('admins','id')->onDelete('cascade');
            $table->foreignId('email_account_id')->constrained('email_accounts','id')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions','id')->onDelete('cascade');
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
        Schema::dropIfExists('user_email_accounts');
    }
};
