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
        Schema::create('customfields', function (Blueprint $table) {
            $table->integer('customfields_id', true);
            $table->dateTime('customfields_created');
            $table->dateTime('customfields_updated');
            $table->integer('customfields_position')->default(1);
            $table->string('customfields_datatype', 50)->nullable()->default('text')->comment('text|paragraph|number|decima|date|checkbox|dropdown');
            $table->text('customfields_datapayload')->nullable()->comment('use this to store dropdown lists etc');
            $table->string('customfields_type', 50)->nullable()->comment('clients|projects|leads|tasks|tickets');
            $table->string('customfields_name', 250)->nullable()->comment('e.g. project_custom_field_1');
            $table->string('customfields_title', 250)->nullable();
            $table->string('customfields_required', 5)->nullable()->default('no')->comment('yes|no - standard form');
            $table->string('customfields_show_client_page', 100)->nullable();
            $table->string('customfields_show_project_page', 100)->nullable();
            $table->string('customfields_show_task_summary', 100)->nullable();
            $table->string('customfields_show_lead_summary', 100)->nullable();
            $table->string('customfields_show_invoice', 100)->nullable();
            $table->string('customfields_show_ticket_page', 100)->nullable()->default('no');
            $table->string('customfields_show_filter_panel', 100)->nullable()->default('no')->comment('yes|no');
            $table->string('customfields_standard_form_status', 100)->nullable()->default('disabled')->comment('disabled | enabled');
            $table->string('customfields_status', 50)->nullable()->default('disabled')->comment('disabled | enabled');
            $table->string('customfields_sorting_a_z', 5)->nullable()->default('z')->comment('hack to get sorting right, excluding null title items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customfields');
    }
};
