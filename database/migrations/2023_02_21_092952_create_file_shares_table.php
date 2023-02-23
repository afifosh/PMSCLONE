<?php

use App\Models\FileShare;
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
        Schema::create('file_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfp_file_id')->constrained('rfp_files')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('admins')->onDelete('cascade');
            $table->foreignId('shared_by')->constrained('admins')->onDelete('cascade');
            $table->enum('permission', array_keys(FileShare::Permissions))->default('view');
            $table->date('expires_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('admins')->onDelete('cascade');
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
        Schema::dropIfExists('file_shares');
    }
};
