<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuManagersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('menu_managers', function (Blueprint $table) {
      $table->id();
      $table->tinyInteger('parent_id')->default(0);
      $table->string('title')->nullable();
      $table->string('slug')->unique()->nullable();
      $table->string('path_url')->nullable();
      $table->string('icon')->nullable();
      $table->enum('type', ['module', 'header', 'line', 'static']);
      $table->string('position')->nullable();
      $table->integer('sort');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('menu_managers');
  }
}
