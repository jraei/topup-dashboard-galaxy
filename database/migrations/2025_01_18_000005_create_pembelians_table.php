
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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('layanan_id')->constrained('layanans');
            $table->string('game_id')->comment('Player ID, Server ID, etc.');
            $table->string('game_zone')->nullable()->comment('Zone ID, Server name, etc.');
            $table->bigInteger('amount');
            $table->bigInteger('price');
            $table->enum('payment_method', ['balance', 'tripay', 'midtrans', 'manual'])->default('balance');
            $table->string('payment_reference')->nullable()->comment('Payment gateway reference');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('callback_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
