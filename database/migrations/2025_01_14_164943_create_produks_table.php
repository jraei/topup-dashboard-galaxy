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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('developer')->nullable();
            $table->string('brand');
            $table->unsignedBigInteger('kategori_id');
            $table->foreign('kategori_id')->references('id')->on('kategoris');
            $table->string('tipe');
            $table->string('slug')->unique();
            $table->string('sistem_id')->nullable();
            $table->string('validasi_id')->default('tidak');
            $table->text('deskripsi_game')->nullable();
            $table->text('petunjuk_field')->nullable();
            $table->string('status')->default('active');
            $table->string('thumbnail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
