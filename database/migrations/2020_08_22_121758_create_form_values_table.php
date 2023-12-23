<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormValuesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('form_values', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->unsignedBigInteger('form_id');
      $table->unsignedBigInteger('user_id')->nullable();
      $table->text('json');
      $table->float('amount')->nullable();
      $table->string('currency_symbol')->nullable();
      $table->string('currency_name')->nullable();
      $table->string('transaction_id')->nullable();
      $table->string('status')->default('pending');
      $table->string('payment_type')->nullable();
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
    Schema::dropIfExists('form_values');
  }
}
