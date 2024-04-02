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
        Schema::create('harga_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kendaraan')->references('id')->on('kendaraan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_harga')->references('id')->on('harga')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('harga_kendaraan');
    }
};
