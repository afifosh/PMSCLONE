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
        Schema::create('email_account_message_addresses', function (Blueprint $table) {
            $table->foreignId('message_id')->constrained('email_account_messages')->cascadeOnDelete();
            $table->string('address')->nullable(); // For drafts without address
            $table->string('name')->nullable();
            $table->string('address_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_account_message_addresses');
    }
};
