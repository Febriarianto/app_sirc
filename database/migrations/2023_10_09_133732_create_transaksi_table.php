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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penyewa')->on('penyewa')->onUpdate('cascade')->onDelete('cascade');
            $table->string('kota_tujuan');
            $table->foreignId('id_kendaraan')->on('kendaraan')->onUpdate('cascade')->onDelete('cascade');
            $table->string('lama_sewa');
            $table->dateTime('keberangkatan');
            $table->dateTime('kepulangan');
            $table->unsignedBigInteger('biaya');
            $table->unsignedBigInteger('dp');
            $table->unsignedBigInteger('sisa');
            $table->string('kondisi_bbm');
            $table->string('dongkrak');
            $table->string('ban_cadangan');
            $table->string('kelengkapan_lain');
            $table->string('jaminan');
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
        Schema::dropIfExists('transaksi');
    }
};
