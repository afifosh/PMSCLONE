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
        Schema::create('payments', function (Blueprint $table) {
            $table->integer('payment_id', true)->comment('[truncate]');
            $table->dateTime('payment_created')->nullable();
            $table->dateTime('payment_updated')->nullable();
            $table->integer('payment_creatorid')->nullable()->index('payment_creatorid')->comment('\'0\' for system');
            $table->date('payment_date')->nullable();
            $table->integer('payment_invoiceid')->nullable()->index('payment_invoiceid')->comment('invoice id');
            $table->integer('payment_subscriptionid')->nullable()->index('payment_subscriptionid')->comment('subscription id');
            $table->integer('payment_clientid')->nullable()->index('payment_clientid');
            $table->integer('payment_projectid')->nullable()->index('payment_projectid');
            $table->decimal('payment_amount', 10);
            $table->string('payment_transaction_id', 100)->nullable();
            $table->string('payment_gateway', 100)->nullable()->index('payment_gateway')->comment('paypal | stripe | cash | bank');
            $table->text('payment_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
