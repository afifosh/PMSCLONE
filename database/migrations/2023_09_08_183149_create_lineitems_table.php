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
        Schema::create('lineitems', function (Blueprint $table) {
            $table->integer('lineitem_id', true);
            $table->integer('lineitem_position')->nullable();
            $table->dateTime('lineitem_created')->nullable();
            $table->dateTime('lineitem_updated')->nullable();
            $table->text('lineitem_description')->nullable();
            $table->string('lineitem_rate', 250)->nullable();
            $table->string('lineitem_unit', 100)->nullable();
            $table->float('lineitem_quantity', 10, 0)->nullable();
            $table->decimal('lineitem_total', 10)->nullable();
            $table->string('lineitemresource_linked_type', 30)->nullable()->index('lineitemresource_linked_type')->comment('task | expense');
            $table->integer('lineitemresource_linked_id')->nullable()->index('lineitemresource_linked_id')->comment('e.g. task id');
            $table->string('lineitemresource_type', 50)->nullable()->index('lineitemresource_type')->comment('[polymorph] invoice | estimate');
            $table->integer('lineitemresource_id')->nullable()->index('lineitemresource_id')->comment('[polymorph] e.g invoice_id');
            $table->string('lineitem_type', 10)->nullable()->default('plain')->index('lineitem_type')->comment('plain|time|dimensions');
            $table->integer('lineitem_time_hours')->nullable();
            $table->integer('lineitem_time_minutes')->nullable();
            $table->text('lineitem_time_timers_list')->nullable()->comment('comma separated list of timers');
            $table->float('lineitem_dimensions_length', 10, 0)->nullable();
            $table->float('lineitem_dimensions_width', 10, 0)->nullable();
            $table->string('lineitem_tax_status', 100)->nullable()->default('taxable')->comment('taxable|exempt  - this is inherited from the product/item setting');
            $table->integer('lineitem_linked_product_id')->nullable()->comment('the original product that created this line item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lineitems');
    }
};
