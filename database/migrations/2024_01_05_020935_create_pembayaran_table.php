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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi')->references('id')->on('transaksi')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('tipe', ['dp', 'pelunasan', 'titip', 'lainnya']);
            $table->unsignedBigInteger('nominal');
            $table->enum('metode', ['cash', 'transfer']);
            $table->string('file')->nullable();
            $table->string('detail')->nullable();
            $table->string('penerima');
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
        Schema::dropIfExists('pembayaran');
    }
};
