<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('admins')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('admins')->cascadeOnDelete();
            $table->string('description');
            $table->dateTime('remind_at');
            $table->dateTime('reminded_at')->nullable();
            $table->boolean('notify_by_email')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_reminders');
    }
};
