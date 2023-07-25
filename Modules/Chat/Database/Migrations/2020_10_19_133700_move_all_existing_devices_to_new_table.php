<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Chat\Models\User;
use Modules\Chat\Models\UserDevice;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // $users = User::whereNotNull('player_id')->orWhere('player_id', '!=', '')->get();
        // foreach ($users as $user) {
        //     $exists = UserDevice::whereUserId($user->id)->wherePlayerId($user->player_id)->first();

        //     if ($exists) {
        //         continue;
        //     }

        //     UserDevice::create([
        //         'user_id' => $user->id,
        //         'player_id' => $user->player_id,
        //     ]);
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new', function (Blueprint $table) {
            //
        });
    }
};
