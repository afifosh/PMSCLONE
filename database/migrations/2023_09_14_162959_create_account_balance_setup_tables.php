<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountBalanceSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_balances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_number', 20)->unique();
            $table->string('name', 255)->nullable();
            $table->char('currency', 3);
            $table->bigInteger('balance')->default(0);
            $table->nullableMorphs('creator');
            $table->timestamps();
        });

        Schema::create('account_balance_holders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('account_balance_id')->constrained('account_balances');
            $table->morphs('holder');
            $table->timestamps();
        });

        Schema::create('account_balance_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('account_balance_id')->constrained('account_balances');
            $table->foreignId('account_balance_extra_id')->nullable()->constrained('account_balances');
            $table->bigInteger('amount')->default(0);
            $table->string('description')->nullable();
            $table->json('data')->nullable();
            $table->integer('type')->unsigned()->nullable()->comment('1-flow, 2-transfer'); // 1 - flow (debit, credit), 2 - transfer (in, out)
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_balance_transactions');
        Schema::dropIfExists('account_balances');
    }
}
