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
        Schema::create('programs_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('added_by')->constrained('admins')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();  // Adds the 'deleted_at' column
            $table->boolean('permanent_access')->default(true);
            $table->timestamp('until_at')->nullable();
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
        Schema::dropIfExists('programs_users');
    }
};
