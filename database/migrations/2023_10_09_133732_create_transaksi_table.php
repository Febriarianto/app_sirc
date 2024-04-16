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
            $table->string('no_inv');
            $table->foreignId('id_penyewa')->references('id')->on('penyewa')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_kendaraan')->references('id')->on('kendaraan')->onUpdate('cascade')->onDelete('cascade');
            $table->string('kota_tujuan')->nullable();
            $table->string('lama_sewa')->nullable();
            $table->string('durasi')->nullable();
            $table->enum('paket', ['tahunan', 'bulanan', 'mingguan', 'harian', 'jam']);
            $table->date('keberangkatan');
            $table->string('estimasi_sewa')->nullable();
            $table->time('estimasi_tgl')->nullable();
            $table->time('estimasi_time')->nullable();
            $table->time('keberangkatan_time')->nullable();
            $table->date('kepulangan')->nullable();
            $table->time('kepulangan_time')->nullable();
            $table->enum('tipe', ['pesan', 'sewa', 'invoice']);
            $table->enum('status', ['pending', 'proses', 'selesai', 'batal']);
            $table->string('jaminan')->nullable();
            $table->unsignedBigInteger('harga_sewa')->nullable();
            $table->unsignedBigInteger('diskon')->nullable();
            $table->unsignedBigInteger('over_time')->nullable();
            $table->unsignedBigInteger('biaya')->nullable();
            $table->unsignedBigInteger('sisa')->nullable();
            $table->unsignedBigInteger('kembalian')->nullable();
            $table->string('keterangan')->nullable();
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
