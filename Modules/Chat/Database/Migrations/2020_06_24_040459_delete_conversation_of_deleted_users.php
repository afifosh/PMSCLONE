<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Chat\Models\Conversation;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Conversation::whereNull('from_id')->orWhere('from_id', '')->delete();

        // Conversation::whereDoesntHave('receiver')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
