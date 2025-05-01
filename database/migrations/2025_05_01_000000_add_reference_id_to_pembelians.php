
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
            $table->string('reference_id')->nullable()->after('order_id');
            $table->string('nickname')->nullable()->change();
            $table->json('callback_data')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('reference_id');
            $table->dropColumn('callback_data');
            $table->string('nickname')->nullable(false)->change();
        });
    }
};
