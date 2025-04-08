<?php

namespace App\Http\Controllers\Admin;

use App\Models\PayMethod;
use ZerosDev\TriPay\Client;
use Illuminate\Http\Request;
use ZerosDev\TriPay\Merchant;
use App\Models\PaymentProvider;
use App\Http\Controllers\Controller;

class TripayController extends Controller
{
    public function getTripayMethod()
    {
        $provider = PaymentProvider::where('provider_name', 'Tripay')->get()[0];
        $config = [
            "mode" => 'development',
            "merchant_code" => $provider['kode_merchant'],
            "api_key" => $provider['api_key'],
            "private_key" => $provider['private_key'],
            "guzzle_options" => []
        ];
        $client = new Client($config);
        $merchant = new Merchant($client);
        $result = $merchant->paymentChannels();

        $data = json_decode($result->getBody()->getContents(), true)['data'];


        foreach ($data as $method) {
            $method = collect($method);
            $methodExist = PayMethod::where('kode', $method['code'])->where('sistem', 'Tripay')->first();
            if (!$methodExist) {
                // masukkan data ke dalam db
                PayMethod::create([
                    "kode" => $method['code'],
                    "tipe" => $method['group'],
                    "metode" => $method['name'],
                    "sistem" => "Tripay",
                    "gambar" => $method['icon_url'],
                    "keterangan" => $method['name'],
                    "active" => $method['active']
                ]);
            } else {
                // jika ada layanan sebelumnya, update harganya
                $methodExist->update([
                    "kode" => $method['code'],
                    "tipe" => $method['group'],
                    "metode" => $method['name'],
                    "gambar" => $method['icon_url'],
                    'active' => ($method['active'] === true ? true : false)
                ]);
            }
        }
    }
}
