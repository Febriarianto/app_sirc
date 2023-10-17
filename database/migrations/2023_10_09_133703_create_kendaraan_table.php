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
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pemilik')->references('id')->on('pemilik')->onUpdate('cascade')->onDelete('cascade');
            $table->string('id_jenis')->references('id')->on('jenis')->onUpdate('cascade')->onDelete('cascade');;
            $table->string('no_kendaraan')->unique();
            $table->string('tahun');
            $table->string('warna');
            $table->string('foto');
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
        Schema::dropIfExists('kendaraan');
    }
};
