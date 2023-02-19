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
        Schema::create('rfp_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfp_id')->constrained('rfp_drafts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('uploaded_by')->constrained('admins')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->string('file');
            $table->string('mime_type')->nullable();
            $table->string('extension')->nullable();
            $table->timestamp('trashed_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('rfp_files');
    }
};
