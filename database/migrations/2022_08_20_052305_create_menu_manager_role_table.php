<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('menu_manager_role', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('menu_manager_id');
      $table->unsignedBigInteger('role_id');
      $table->foreign('menu_manager_id')->references('id')->on('menu_managers')->onUpdate('cascade')->onDelete('cascade');
      $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('menu_manager_role');
  }
};
