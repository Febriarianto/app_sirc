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
            $table->foreignId('id_kendaraan')->references('id')->on('kendaraan')->onUpdate('cascade')->onDelete('cascade');
            $table->string('kota_tujuan')->nullable();
            $table->string('lama_sewa')->nullable();
            $table->enum('paket', ['tahunan', 'bulanana', 'mingguan', 'harian','jam']);
            $table->date('keberangkatan');
            $table->time('keberangkatan_time')->nullable();
            $table->date('kepulangan')->nullable();
            $table->time('kepulangan_time')->nullable();
            $table->unsignedBigInteger('dp');
            $table->enum('metode_dp', ['cash', 'transfer']);
            $table->string('bukti_dp')->nullable();
            $table->enum('tipe', ['pemesanan', 'sewa',]);
            $table->enum('status', ['pending', 'proses', 'selesai', 'batal']);
            $table->unsignedBigInteger('harga_sewa')->nullable();
            $table->unsignedBigInteger('over_time')->nullable();
            $table->unsignedBigInteger('biaya')->nullable();
            $table->unsignedBigInteger('sisa')->nullable();
            $table->enum('metode_pelunasan', ['cash', 'transfer','lainnya'])->nullable();
            $table->string('keterangan')->nullable();
            $table->string('bukti_pelunasan')->nullable();
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
