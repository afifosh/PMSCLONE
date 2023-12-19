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
            $table->string('account_number', 16);
            $table->string('name', 255)->nullable();
            $table->char('currency', 3);
            $table->bigInteger('balance')->default(0);
            $table->nullableMorphs('creator');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('account_balance_permissions', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->foreignId('account_balance_id')->constrained('account_balances');
          $table->integer('permission')->comment('can pay:1 regular invoice; 2: RC; 3: WHT');
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
            $table->nullableMorphs('related'); // contract, invoice, payment, etc
            $table->bigInteger('amount')->default(0);
            $table->bigInteger('remaining_balance')->default(0);
            $table->string('title')->nullable();
            $table->json('data')->nullable();
            $table->string('description')->nullable();
            $table->enum('type', ['Credit', 'Debit']);
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
        Schema::dropIfExists('account_balance_transactions');
        Schema::dropIfExists('account_balances');
    }
}
