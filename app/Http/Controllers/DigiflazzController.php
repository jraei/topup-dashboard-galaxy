<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Produk;
use App\Models\Layanan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Gonon\Digiflazz\Digiflazz;
use Gonon\Digiflazz\PriceList;

class DigiflazzController extends Controller
{
    public function __construct()
    {
        Digiflazz::initDigiflazz(env('DIGIFLAZZ_USERNAME'), env('DIGIFLAZZ_APIKEY'));
    }

    public function getDigiflazzService()
    {
        $res = PriceList::getPrePaid();


        foreach (Produk::get() as $produk) {
            foreach ($res as $data) {
                $data = collect($data);
                $produk = collect($produk);
                if (Str::upper($data['brand']) == Str::upper($produk['brand'])) {

                    $layananExist = Layanan::where('kode_produk', $data['buyer_sku_code'])->first();
                    $params = [
                        "produk_id" => $produk['id'],
                        "provider" =>  "digiflazz",
                        "layanan" => $data['product_name'],
                        "kode_produk" => $data['buyer_sku_code'],
                        "harga_beli" => $data['price'],
                        "status" => ($data['seller_product_status'] === true ? "available" : "unavailable")
                    ];

                    // set harga
                    if ($data['category'] == "Games") {
                        $price = [
                            "harga_guest" => round($data['price'] + ($data['price'] * env('PROFIT_GAMES_GUEST') / 100)),
                            "harga_basic" => round($data['price'] + ($data['price'] * env('PROFIT_GAMES_BASIC') / 100)),
                            "harga_premium" => round($data['price'] + ($data['price'] * env('PROFIT_GAMES_PREMIUM') / 100)),
                            "harga_h2h" => round($data['price'] + ($data['price'] * env('PROFIT_GAMES_H2H') / 100)),
                        ];
                    } else if ($data['category'] == "Pulsa") {
                        $price = [
                            "harga_guest" => round($data['price'] + env('PROFIT_PULSA_GUEST')),
                            "harga_basic" => round($data['price'] + env('PROFIT_PULSA_BASIC')),
                            "harga_premium" => round($data['price'] + env('PROFIT_PULSA_PREMIUM')),
                            "harga_h2h" => round($data['price'] + env('PROFIT_PULSA_H2H')),
                        ];
                    } else if ($data['category'] == "E-Money") {
                        $price = [
                            "harga_guest" => round($data['price'] + env('PROFIT_EMONEY_GUEST')),
                            "harga_basic" => round($data['price'] + env('PROFIT_EMONEY_BASIC')),
                            "harga_premium" => round($data['price'] + env('PROFIT_EMONEY_PREMIUM')),
                            "harga_h2h" => round($data['price'] + env('PROFIT_EMONEY_H2H')),
                        ];
                    }

                    // Merge params
                    $mergedParams = array_merge($params, $price);


                    // cek layanan sudah ada apa belum
                    if (!$layananExist) {
                        // masukkan data ke dalam db
                        Layanan::create($mergedParams);
                    } else {
                        // jika ada layanan sebelumnya, update harganya
                        $layananExist->update([
                            'harga' => $data['price'],
                            'status' => ($data['seller_product_status'] === true ? "available" : "unavailable")
                        ]);
                    }
                }
            }
        }
    }

    public function getDigiflazzProduk()
    {

        $res = \Gonon\Digiflazz\PriceList::getPrePaid(); // Prepaid product

        $arrGame = [];
        $arrPulsa = [];

        foreach ($res as $game) {
            $game = collect($game);
            if ($game['category'] == "Games") {
                array_push($arrGame, $game['brand']);
            } else if ($game['category'] == "Pulsa") {
                array_push($arrPulsa, $game['brand']);
            }
        }



        foreach (array_unique($arrGame) as $game) {

            try {
                $tes = Produk::create([
                    "nama" => $game,
                    "brand" => $game,
                    "kelompok" => "1",
                    "tipe" => "game",
                    "slug" => Str::slug($game, '-'),
                    "thumbnail" => "null"
                ]);
            } catch (Exception $e) {
                throw new Exception("Gagal menambahkan produk GAME digiflazz! Pesan: " . $e->getMessage());
            }
        }

        foreach (array_unique($arrPulsa) as $pulsa) {
            try {
                Produk::create([
                    "nama" => $pulsa,
                    "brand" => $pulsa,
                    "kelompok" => "voucher",
                    "tipe" => "pulsa",
                    "slug" => Str::slug($pulsa, '-'),
                    "thumbnail" => "null",
                ]);
            } catch (\Exception $e) {
                throw new Exception("Gagal menambahkan produk PULSA digiflazz! Pesan: " . $e->getMessage());
            }
        }
    }
}
