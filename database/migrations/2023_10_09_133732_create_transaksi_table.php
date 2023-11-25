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
            $table->foreignId('id_penyewa')->references('id')->on('penyewa')->onUpdate('cascade')->onDelete('cascade');
            $table->string('kota_tujuan');
            $table->foreignId('id_kendaraan')->references('id')->on('kendaraan')->onUpdate('cascade')->onDelete('cascade');
            $table->string('lama_sewa');
            $table->enum('paket', ['tahunan', 'bulanana', 'mingguan', 'harian']);
            $table->date('keberangkatan');
            $table->date('kepulangan');
            $table->unsignedBigInteger('dp');
            $table->enum('metode_dp', ['cash', 'transfer']);
            $table->string('bukti_dp');
            $table->enum('tipe', ['pemesanan', 'sewa',]);
            $table->enum('status', ['pending','proses', 'selesai', 'batal']);
            $table->unsignedBigInteger('harga_sewa');
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
