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
        Schema::create('device_authorizations', function (Blueprint $table) {
            $table->string('uuid')->primary();
            $table->text('fingerprint');            
            $table->morphs('authenticatable', 'authenticatable_index');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('location')->nullable();     
            $table->string('token')->nullable();
            $table->integer('attempts')->unsigned()->default(1);
            $table->integer('failed_attempts')->unsigned()->default(0); // add failed_attempts as integer
            $table->boolean('safe')->default(true); 
            $table->boolean('authorized')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamps();
            $table->softDeletes();        
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_authorizations');
    }
};
