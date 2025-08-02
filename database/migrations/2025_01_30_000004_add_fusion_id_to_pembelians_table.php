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
        Schema::table('pembelians', function (Blueprint $table) {
            $table->foreignId('fusion_service_id')->nullable()->constrained('fusion_services')->nullOnDelete()->after('layanan_id');
            $table->json('fusion_transaction_data')->nullable()->after('callback_data')->comment('Individual service transaction details for fusion orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropForeign(['fusion_service_id']);
            $table->dropColumn(['fusion_service_id', 'fusion_transaction_data']);
        });
    }
};