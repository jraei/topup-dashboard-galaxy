
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
        Schema::create('profit_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->decimal('profit_guest', 10, 2)->default(10.00); // Default 10%
            $table->decimal('profit_basic', 10, 2)->default(8.00);  // Default 8%
            $table->decimal('profit_premium', 10, 2)->default(5.00); // Default 5%
            $table->decimal('profit_h2h', 10, 2)->default(3.00);     // Default 3% 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_produks');
    }
};
