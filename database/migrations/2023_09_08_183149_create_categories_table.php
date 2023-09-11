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
        Schema::create('categories', function (Blueprint $table) {
            $table->integer('category_id', true)->comment('[do not truncate] - only delete where category_system_default = no');
            $table->dateTime('category_created')->nullable();
            $table->dateTime('category_updated')->nullable();
            $table->integer('category_creatorid')->nullable()->index('category_creatorid');
            $table->string('category_name', 150)->nullable();
            $table->string('category_description', 150)->nullable()->comment('optional (mainly used by knowledge base)');
            $table->string('category_system_default', 20)->nullable()->default('no')->comment('yes | no (system default cannot be deleted)');
            $table->string('category_visibility', 20)->nullable()->default('everyone')->comment('everyone | team | client (mainly used by knowledge base)');
            $table->string('category_icon', 100)->nullable()->default('sl-icon-docs')->comment('optional (mainly used by knowledge base)');
            $table->string('category_type', 50)->index('category_type')->comment('project | client | contract | expense | invoice | lead | ticket | item| estimate | knowledgebase');
            $table->string('category_slug', 250);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
