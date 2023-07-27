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
        Schema::create('conversations', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('from_id')->nullable()->constrained('admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('to_id')->nullable();
            $table->string('to_type')->default(\Modules\Chat\Models\Conversation::class)->comment('1 => Message, 2 => Group Message');
            $table->unsignedInteger('reply_to')->nullable();
            $table->text('message');
            $table->tinyInteger('status')->default(0)->comment('0 for unread,1 for seen');
            $table->tinyInteger('message_type')->default(0)->comment('0- text message, 1- image, 2- pdf, 3- doc, 4- voice');
            $table->text('file_name')->nullable();
            $table->text('url_details')->nullable();
            $table->timestamps();

            $table->foreign('reply_to')->references('id')->on('conversations')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};
