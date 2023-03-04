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
        Schema::create('company_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->integer('locality_type')->nullable()->comment('1: foreign, 2: local');
            $table->string('geographical_coverage')->nullable();
            $table->string('sa_name')->nullable();
            $table->string('logo')->nullable();
            $table->date('year_founded')->nullable();
            $table->string('duns_number')->nullable();
            $table->string('no_of_employees')->nullable();
            $table->string('legal_form')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->text('description')->nullable();
            $table->string('parent_company')->nullable();
            $table->string('subsidaries')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('company_details');
    }
};
