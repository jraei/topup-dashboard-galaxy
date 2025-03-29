<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name')->default('basic');;
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // Insert default roles
        DB::table('user_roles')->insert([
            ['role_name' => 'admin', 'display_name' => 'Administrator', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'basic', 'display_name' => 'Basic User', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'premium', 'display_name' => 'Premium User', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
