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
        Schema::create('pay_methods', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('tipe');
            $table->string('metode');
            $table->string('sistem');
            $table->string('gambar');
            $table->bigInteger('norek')->nullable();
            $table->text('keterangan');
            $table->boolean('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_methods');
    }
};
