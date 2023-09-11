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
        Schema::create('reminders', function (Blueprint $table) {
            $table->integer('reminder_id', true);
            $table->dateTime('reminder_created');
            $table->dateTime('reminder_updated');
            $table->integer('reminder_userid')->nullable();
            $table->dateTime('reminder_datetime')->nullable();
            $table->timestamp('reminder_timestamp')->nullable();
            $table->string('reminder_title', 250)->nullable();
            $table->string('reminder_meta', 250)->nullable();
            $table->text('reminder_notes')->nullable();
            $table->string('reminder_status', 10)->nullable()->default('new')->index('reminder_status')->comment('active|due');
            $table->string('reminder_sent', 10)->nullable()->default('no')->index('reminder_sent')->comment('yes|no');
            $table->string('reminderresource_type', 50)->nullable()->index('reminderresource_type')->comment('project|client|estimate|lead|task|invoice|ticket');
            $table->integer('reminderresource_id')->nullable()->index('reminderresource_id')->comment('linked resoucre id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reminders');
    }
};
