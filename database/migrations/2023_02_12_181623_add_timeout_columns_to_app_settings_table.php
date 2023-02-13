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
            if (! Schema::hasColumn('settings', 'timeout_after_seconds')) {
                $table->integer('timeout_after_seconds')->nullable()->after('password_expire_days');
            }

            if (! Schema::hasColumn('settings', 'timeout_warning_seconds')) {
                $table->integer('timeout_warning_seconds')->nullable()->after('password_expire_days');
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
            if (Schema::hasColumn('settings', 'timeout_after_seconds')) {
                $table->dropColumn('timeout_after_seconds');
            }

            if (Schema::hasColumn('settings', 'timeout_warning_seconds')) {
                $table->dropColumn('timeout_warning_seconds');
            }
        });
    }
};
