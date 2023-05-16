<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

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
        Schema::create('user_ordered_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('display_order')->index();
            $table->foreignId('user_id')->constraine('users')->cascadeOnDelete();
            $table->morphs('orderable');
            $table->unique(['user_id', 'orderable_id', 'orderable_type'], 'unique_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @codeCoverageIgnore
     */
    public function down(): void
    {
        Schema::dropIfExists('user_ordered_models');
    }
};
