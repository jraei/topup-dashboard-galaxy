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
        Schema::create('flashsale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flashsale_event_id')->nullable()->constrained('flashsale_events')->nullOnDelete();
            $table->foreignId('layanan_id')->nullable()->constrained('layanans')->nullOnDelete();
            $table->bigInteger('harga_flashsale')->default(0);
            $table->bigInteger('stok_tersedia')->nullable();
            $table->bigInteger('stok_terjual')->nullable();
            $table->bigInteger('batas_user')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashsale_items');
    }
};
