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
        Schema::table('app_settings', function (Blueprint $table) {
            if(! Schema::hasColumn('app_settings', 'password_history_count')) {
                $table->tinyInteger('password_history_count')->after('password_expire_days')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_settings', function (Blueprint $table) {
            if(Schema::hasColumn('app_settings', 'password_history_count')) {
                $table->dropColumn('password_history_count');
            }
        });
    }
};
