<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('layanans', function (Blueprint $table) {
            $table->id()->unique();
            $table->string('kategori_id');
            $table->string('provider');
            $table->string('layanan');
            $table->string('kode_produk')->unique();
            $table->bigInteger('harga_beli');
            $table->bigInteger('harga_guest');
            $table->bigInteger('harga_basic');
            $table->bigInteger('harga_premium');
            $table->bigInteger('harga_h2h');
            $table->string('status')->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanans');
    }
};
