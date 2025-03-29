<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Provider;
use App\Models\SubKategori;
use Illuminate\Support\Str;
use App\Models\PaymentProvider;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        User::factory()->create([
            'username' => 'justin',
            'email' => 'justin@gmail.com',
            'password' => bcrypt('123justin'),
            'phone' => '081932888380',
            'user_role_id' => 1,
            'status' => 'active'
        ]);

        User::factory()->count(50)->create();

        PaymentProvider::create([
            'provider_name' => 'Tripay',
            'kode_merchant' => env('TRIPAY_MERCHANT_CODE'),
            'api_key' => env('TRIPAY_APIKEY'),
            'private_key' => env('TRIPAY_PRIVATEKEY')
        ]);

        Kategori::create([
            "nama_kategori" => "Top up"
        ]);
        Kategori::create([
            "nama_kategori" => "Mobile legends specialist"
        ]);


        Provider::create([
            "provider_name" => "digiflazz",
            "api_key" => "eeeufytr2c-jassstinnnn-justin7765eerf",
            "api_username" => "figojugqYaVg"
        ]);


        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
