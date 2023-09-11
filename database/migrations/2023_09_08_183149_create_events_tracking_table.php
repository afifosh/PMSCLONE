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
        Schema::create('events_tracking', function (Blueprint $table) {
            $table->integer('eventtracking_id', true);
            $table->dateTime('eventtracking_created');
            $table->dateTime('eventtracking_updated');
            $table->integer('eventtracking_eventid')->index('eventtracking_eventid');
            $table->integer('eventtracking_userid')->index('eventtracking_userid');
            $table->string('eventtracking_status', 30)->nullable()->default('unread')->index('eventtracking_status')->comment('read|unread');
            $table->string('eventtracking_email', 50)->nullable()->default('no')->comment('yes|no');
            $table->string('eventtracking_source', 50)->nullable()->comment('the actual item (e.g. file | comment | invoice)');
            $table->string('eventtracking_source_id', 50)->nullable()->comment('the id of the actual item');
            $table->string('parent_type', 50)->nullable()->index('parent_type')->comment('used to locate the main event in the events table. Also used for marking the event as read, once the parent has been viewed. (e.g. for invoice, parent is invoice. For comment task, parent is task)');
            $table->integer('parent_id')->nullable()->index('parent_id');
            $table->string('resource_type', 50)->nullable()->index('resource_type')->comment('Also used for marking events as read, for ancillary items like (project comments, project file) where just viewing a project is enough');
            $table->integer('resource_id')->nullable()->index('resource_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events_tracking');
    }
};
