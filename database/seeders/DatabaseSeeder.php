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
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // bikin role spatie
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'basic']);
        Role::create(['name' => 'premium']);

        User::factory()->create([
            'username' => 'justin',
            'email' => 'justin@gmail.com',
            'password' => bcrypt('123justin'),
            'phone' => '081932888380',
            'level' => 'admin',
            'status' => 'active'
        ])->assignRole('admin');

        User::factory()->count(50)->create()->each(function ($user) {
            $user->assignRole('basic');
        });


        PaymentProvider::create([
            'provider_name' => 'Tripay',
            'kode_merchant' => env('TRIPAY_MERCHANT_CODE'),
            'api_key' => env('TRIPAY_APIKEY'),
            'private_key' => env('TRIPAY_PRIVATEKEY')
        ]);

        Kategori::create([
            "kategori_name" => "Game Mobile"
        ]);
        Kategori::create([
            "kategori_name" => "Game PC"
        ]);

        SubKategori::create([
            "sub_kategori" => "Via Id",
            "kategori_id" => 1
        ]);
        SubKategori::create([
            "sub_kategori" => "Via Login",
            "kategori_id" => 2
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
