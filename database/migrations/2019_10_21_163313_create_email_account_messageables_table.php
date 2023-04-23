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
        Schema::create('email_account_messageables', function (Blueprint $table) {
            $table->foreignId('message_id')->constrained('email_account_messages');
            $table->morphs('messageable', 'email_account_messageables_index');
            $table->primary(
                ['message_id', 'messageable_id', 'messageable_type'],
                'messageable_primary'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_account_messageables');
    }
};
