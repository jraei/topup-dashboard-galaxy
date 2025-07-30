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
        Schema::table('paket_layanans', function (Blueprint $table) {
            $table->dropForeign(['produk_id']);
            $table->dropColumn('produk_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paket_layanans', function (Blueprint $table) {
            $table->foreignId('produk_id')->nullable()->constrained('produks')->nullOnDelete()->after('judul_paket');
        });
    }
};