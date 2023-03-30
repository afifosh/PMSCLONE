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
        Schema::create('email_account_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreignId('email_account_id')->constrained('email_accounts');
            $table->string('remote_id')->nullable()
                ->comment('API ID, uidvalidity etc...');
            $table->boolean('support_move')->default(true);
            $table->boolean('syncable')->default(false);
            $table->boolean('selectable')->default(false);
            $table->string('type')->nullable();
            $table->string('name');
            $table->string('display_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_account_folders');
    }
};
