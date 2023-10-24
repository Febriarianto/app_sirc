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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penyewa')->references('id')->on('penyewa')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_kendaraan')->references('id')->on('kendaraan')->onUpdate('cascade')->onDelete('cascade');
            $table->string('kota_tujuan');
            $table->dateTime('keberangkatan');
            $table->dateTime('kepulangan');
            $table->enum('paket', ['harian', 'mingguan', 'bulanan']);
            $table->integer('harga_paket');
            $table->integer('lama_sewa');
            $table->integer('biaya_overtime');
            $table->integer('total_biaya');
            $table->integer('dp');
            $table->string('bukti_dp');
            $table->enum('metode_pelunasan', ['cash', 'transfer']);
            $table->string('bukti_pelunasan');

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
        Schema::dropIfExists('invoices');
    }
};
