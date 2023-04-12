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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('two_factor_email_confirmed')->default(false);            
            $table->string('two_factor_code')->nullable()->comment('when authenticator is email');
            $table->dateTime('two_factor_expires_at')->nullable();
            $table->dateTime('two_factor_email_confirmed_at')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_merge([
                'two_factor_email_confirmed',
                'two_factor_code',
                'two_factor_expires_at', 
                'two_factor_email_confirmed_at',                 
            ], ));
        });

    }
};
