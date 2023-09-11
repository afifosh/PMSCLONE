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
        Schema::create('invoices', function (Blueprint $table) {
            $table->integer('bill_invoiceid', true);
            $table->dateTime('bill_created')->nullable();
            $table->dateTime('bill_updated')->nullable();
            $table->date('bill_date_sent_to_customer')->nullable()->comment('the date an invoice was published or lasts emailed to the customer');
            $table->integer('bill_clientid')->index('invoice_clientid');
            $table->integer('bill_projectid')->nullable()->index('invoice_projectid')->comment('optional');
            $table->integer('bill_subscriptionid')->nullable()->index('bill_subscriptionid')->comment('optional');
            $table->integer('bill_creatorid')->index('invoice_creatorid');
            $table->integer('bill_categoryid')->default(4)->index('invoice_categoryid');
            $table->date('bill_date');
            $table->date('bill_due_date')->nullable();
            $table->decimal('bill_subtotal', 10)->default(0);
            $table->string('bill_discount_type', 30)->nullable()->default('none')->comment('amount | percentage | none');
            $table->decimal('bill_discount_percentage', 10)->nullable()->default(0)->comment('actual amount or percentage');
            $table->decimal('bill_discount_amount', 10)->nullable()->default(0);
            $table->decimal('bill_amount_before_tax', 10)->nullable()->default(0);
            $table->string('bill_tax_type', 20)->nullable()->default('summary')->comment('summary|inline|none');
            $table->decimal('bill_tax_total_percentage', 10)->nullable()->default(0)->comment('percentage');
            $table->decimal('bill_tax_total_amount', 10)->nullable()->default(0)->comment('amount');
            $table->string('bill_adjustment_description', 250)->nullable();
            $table->decimal('bill_adjustment_amount', 10)->nullable()->default(0);
            $table->decimal('bill_final_amount', 10)->nullable()->default(0);
            $table->text('bill_notes')->nullable();
            $table->text('bill_terms')->nullable();
            $table->string('bill_status', 50)->default('draft')->index('invoice_status')->comment('draft | due | overdue | paid | part_paid');
            $table->string('bill_recurring', 50)->nullable()->default('no')->index('invoice_recurring')->comment('yes|no');
            $table->integer('bill_recurring_duration')->nullable()->comment('e.g. 20 (for 20 days)');
            $table->string('bill_recurring_period', 30)->nullable()->comment('day | week | month | year');
            $table->integer('bill_recurring_cycles')->nullable()->comment('0 for infinity');
            $table->integer('bill_recurring_cycles_counter')->nullable()->comment('number of times it has been renewed');
            $table->date('bill_recurring_last')->nullable()->comment('date when it was last renewed');
            $table->date('bill_recurring_next')->nullable()->comment('date when it will next be renewed');
            $table->string('bill_recurring_child', 5)->nullable()->default('no')->comment('yes|no');
            $table->integer('bill_recurring_parent_id')->nullable()->index('bill_recurring_parent_id')->comment('if it was generated from a recurring invoice, the id of parent invoice');
            $table->string('bill_overdue_reminder_sent', 5)->nullable()->default('no')->comment('yes | no');
            $table->string('bill_invoice_type', 30)->nullable()->default('onetime')->index('bill_invoice_type')->comment('onetime | subscription');
            $table->string('bill_type', 20)->nullable()->default('invoice')->index('bill_type')->comment('invoice|estimate');
            $table->string('bill_visibility', 20)->nullable()->default('visible')->index('bill_visibility')->comment('visible|hidden (used to prevent invoices that are still being cloned from showing in invoices list)');
            $table->string('bill_cron_status', 20)->nullable()->default('none')->index('bill_cron_status')->comment('none|processing|completed|error  (used to prevent collisions when recurring invoiced)');
            $table->dateTime('bill_cron_date')->nullable()->comment('date when cron was run');
            $table->string('bill_viewed_by_client', 20)->nullable()->default('no')->index('bill_viewed_by_client')->comment('yes|no');
            $table->string('bill_system', 20)->nullable()->default('no')->comment('yes|no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
