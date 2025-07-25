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
        Schema::create('profit_paket_layanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_roles_id')->nullable()->constrained('user_roles')->nullOnDelete();
            $table->foreignId('paket_layanan_id')->nullable()->constrained('paket_layanans')->nullOnDelete();
            $table->enum('type', ['percent', 'multiplier'])->default('percent');
            $table->decimal('value', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_paket_layanans');
    }
};
