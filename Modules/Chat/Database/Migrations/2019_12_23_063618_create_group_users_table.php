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
        Schema::create('group_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('group_id');
            // $table->unsignedInteger('user_id');
            $table->foreignId('user_id')->constrained('admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('role')->default(1)->index();
            // $table->unsignedInteger('added_by');
            $table->foreignId('added_by')->constrained('admins')->cascadeOnUpdate()->cascadeOnDelete();
            // $table->unsignedInteger('removed_by')->nullable();
            $table->foreignId('removed_by')->nullable()->constrained('admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('group_id')
                ->references('id')->on('groups')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            // $table->foreign('user_id')
            //     ->references('id')->on('users')
            //     ->onUpdate('cascade')
            //     ->onDelete('cascade');

            // $table->foreign('removed_by')
            //     ->references('id')->on('users')
            //     ->onUpdate('cascade')
            //     ->onDelete('cascade');

            // $table->foreign('added_by')
            //     ->references('id')->on('users')
            //     ->onUpdate('cascade')
            //     ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_users');
    }
};
