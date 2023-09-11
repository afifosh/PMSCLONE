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
        Schema::create('tags', function (Blueprint $table) {
            $table->integer('tag_id', true);
            $table->dateTime('tag_created')->nullable();
            $table->dateTime('tag_updated')->nullable();
            $table->integer('tag_creatorid')->nullable()->index('tag_creatorid');
            $table->string('tag_title', 100);
            $table->string('tag_visibility', 50)->default('user')->index('tag_visibility')->comment('public | user  (public tags are only created via admin settings)');
            $table->string('tagresource_type', 50)->index('tagresource_type')->comment('[polymorph] invoice | project | client | lead | task | estimate | ticket | contract | note | subscription | contract | proposal');
            $table->integer('tagresource_id')->index('tagresource_id')->comment('[polymorph] e.g project_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
};
