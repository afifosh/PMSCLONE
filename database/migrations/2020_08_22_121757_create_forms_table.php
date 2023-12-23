<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('forms', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('title');
      $table->string('logo')->nullable();
      $table->text('description')->nullable();
      $table->string('email')->nullable();
      $table->string('ccemail')->nullable();
      $table->string('bccemail')->nullable();
      $table->text('success_msg')->nullable();
      $table->text('thanks_msg')->nullable();
      $table->integer('is_active')->default(1);
      $table->bigInteger('allow_comments')->nullable();
      $table->bigInteger('allow_share_section')->nullable();
      $table->boolean('payment_status')->default(0);
      $table->string('payment_type')->nullable();
      $table->string('assign_type')->nullable();
      $table->string('created_by')->nullable();
      $table->float('amount')->nullable();
      $table->string('currency_symbol')->nullable();
      $table->string('currency_name')->nullable();
      $table->text('json');
      $table->string('theme')->default('theme1');
      $table->string('theme_color')->default('theme-2');
      $table->string('theme_background_image')->default('form-themes/theme3/form-background.png');
      $table->string('set_end_date')->default(0)->comments('1-On 0-off');
      $table->dateTime('set_end_date_time')->nullable();
      $table->tinyInteger('conditional_rule')->default(1)->comment('1 - Enable / 0 - Disbale');
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
    Schema::dropIfExists('forms');
  }
}
