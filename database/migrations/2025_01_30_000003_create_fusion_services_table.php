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
        Schema::create('fusion_services', function (Blueprint $table) {
            $table->id();
            $table->string('nama_fusion');
            $table->text('deskripsi')->nullable();
            $table->foreignId('paket_layanan_id')->constrained('paket_layanans')->onDelete('cascade');
            $table->json('layanan_ids'); // Array of service IDs to combine
            $table->decimal('custom_price', 15, 2)->nullable(); // Optional custom price override
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fusion_services');
    }
};