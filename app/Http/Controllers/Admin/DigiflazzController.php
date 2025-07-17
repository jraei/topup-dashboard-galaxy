<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\Produk;
use GuzzleHttp\Client;
use App\Models\Layanan;
use App\Models\Kategori;
use App\Models\Provider;
use App\Models\Pembelian;
use App\Models\Pembayaran;
use Illuminate\Log\Logger;
use Illuminate\Support\Str;
use Gonon\Digiflazz\Balance;
use Illuminate\Http\Request;
use Gonon\Digiflazz\Digiflazz;
use Gonon\Digiflazz\PriceList;
use Gonon\Digiflazz\Transaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Gonon\Digiflazz\Exceptions\ApiException;
use App\Http\Controllers\NaelstoreController;

class DigiflazzController extends Controller
{
    public function __construct()
    {
        $digiflazz = Provider::where('provider_name', 'digiflazz')->first();
        Digiflazz::initDigiflazz($digiflazz->api_username, $digiflazz->api_key);
    }

    public function getDigiflazzBalance()
    {
        try {
            $balance = Balance::getBalance();

            return collect([
                'status' => true,
                'data' => $balance->deposit
            ]);
        } catch (\Exception $e) {
            logger()->error("Failed to get balance: " . $e->getMessage());
            return  collect([
                'status' => false,
                'message' => $e->getMessage()
            ]);
            // return $balance->deposit;
        }
    }

    public function createTransaction(array $data)
    {
        // try catch
        try {
            $params = [
                'buyer_sku_code' => $data['kode_layanan'],
                'customer_no' => $data['target'],
                'ref_id' => $data['ref_id'],
                'testing' => $data['testing'] ?? false
            ];
            $result = Transaction::createTransaction($params);

            return collect($result);
        } catch (ApiException $e) {
            logger()->error("Failed to create transaction: " . $e->getMessage());
            return collect([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getDigiflazzService()
    {
        // Ambil provider digiflazz sekaligus dengan validasi
        $digiflazz = Provider::where('provider_name', 'digiflazz')->first();
        if (!$digiflazz) {
            return [
                'status' => false,
                'message' => 'Provider Digiflazz not found'
            ];
        }

        try {
            $services = PriceList::getPrePaid();
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to fetch Digiflazz services: ' . $e->getMessage()
            ];
        }

        // Index semua produk aktif untuk pencarian cepat
        $activeProducts = Produk::where('provider_id', $digiflazz->id)
            ->where('status', 'active')
            ->pluck('id', 'reference')
            ->mapWithKeys(function ($id, $reference) {
                return [Str::upper($reference) => $id];
            });

        // Index layanan yang sudah ada untuk update
        $existingServices = Layanan::where('provider_id', $digiflazz->id)
            ->pluck('id', 'kode_layanan')
            ->toArray();

        // Siapkan batch insert dan update
        $batchInsert = [];
        $batchUpdate = [];
        $now = now();

        foreach ($services as $data) {
            $brand = Str::upper($data->brand ?? '');
            $skuCode = $data->buyer_sku_code ?? null;

            // Validasi data penting
            if (empty($brand)) continue;
            if (empty($skuCode)) continue;
            if (!isset($activeProducts[$brand])) continue;

            $productId = $activeProducts[$brand];
            $status = $data->seller_product_status === true ? 'active' : 'inactive';

            $serviceData = [
                'produk_id' => $productId,
                'provider_id' => $digiflazz->id,
                'kode_layanan' => $skuCode,
                'nama_layanan' => $data->product_name,
                'harga_beli' => $data->price,
                'status' => $status,
                'updated_at' => $now
            ];

            if (isset($existingServices[$skuCode])) {
                $batchUpdate[] = $serviceData + ['id' => $existingServices[$skuCode]];
            } else {
                $batchInsert[] = $serviceData + ['created_at' => $now];
            }
        }

        // Eksekusi batch update dan insert
        $affectedRows = 0;

        if (!empty($batchUpdate)) {
            $affectedRows += $this->batchUpdateLayanan($batchUpdate);
        }

        if (!empty($batchInsert)) {
            Layanan::insert($batchInsert);
            $affectedRows += count($batchInsert);
        }

        return [
            'status' => true,
            'message' => 'Services synchronized successfully',
            'data' => [
                'total_services' => count($services),
                'updated' => count($batchUpdate),
                'created' => count($batchInsert),
                'affected_rows' => $affectedRows
            ]
        ];
    }

    /**
     * Batch update layanan dengan single query
     */
    protected function batchUpdateLayanan(array $data)
    {
        $table = (new Layanan())->getTable();
        $cases = [];
        $ids = [];
        $priceParams = [];
        $statusParams = [];

        foreach ($data as $row) {
            $ids[] = $row['id'];
            $cases[] = "WHEN {$row['id']} THEN ?";
            $priceParams[] = $row['harga_beli'];
            $statusParams[] = $row['status'];
        }

        $ids = implode(',', $ids);
        $cases = implode(' ', $cases);

        return DB::update("UPDATE {$table} SET 
        harga_beli = CASE id {$cases} END,
        status = CASE id {$cases} END,
        updated_at = NOW()
        WHERE id IN ({$ids})", array_merge($priceParams, $statusParams));
    }

    public function getDigiflazzProduk()
    {
        $provider = Provider::where('provider_name', 'digiflazz')->first();

        if (!$provider) {
            return [
                'status' => false,
                'message' => 'Provider not found!'
            ];
        }

        try {
            $res = \Gonon\Digiflazz\PriceList::getPrePaid();
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to fetch Digiflazz products: ' . $e->getMessage()
            ];
        }

        // Developer mapping
        $developerList = [
            "Mobile Legends" => "Moonton",
            "Mobile Legends: Bang Bang" => "Moonton",
            "Free Fire" => "Garena",
            "PUBG Mobile" => "Tencent",
            "PUBG" => "Tencent",
            "Call of Duty Mobile" => "Activision",
            "Call of Duty" => "Activision",
            "Fortnite" => "Epic Games",
            "Valorant" => "Riot Games",
            "Clash of Clans" => "Supercell",
            "Clash Royale" => "Supercell",
        ];

        // Kategori default untuk brand yang tidak punya developer
        $defaultDeveloper = 'Unknown';

        // Group produk berdasarkan kategori
        $groupedProducts = [];
        foreach ($res as $item) {
            if (empty($item->category) || empty($item->brand)) {
                continue;
            }
            $groupedProducts[$item->category][] = $item->brand;
        }

        // Counter hasil
        $result = [
            'total_categories' => 0,
            'total_products' => 0,
            'categories' => []
        ];

        // Proses setiap kategori
        foreach ($groupedProducts as $categoryName => $brands) {
            // Buat kategori jika belum ada
            $kategori = Kategori::firstOrCreate(
                ['kategori_name' => $categoryName],
                ['provider_id' => $provider->id],
                ['status' => 'active']
            );

            $categoryResult = [
                'name' => $categoryName,
                'products' => 0,
                'updated' => 0,
                'created' => 0
            ];

            // Proses brand unik dalam kategori
            foreach (array_unique($brands) as $brand) {
                $developer = $developerList[$brand] ?? $defaultDeveloper;

                // Generate slug unik
                $baseSlug = Str::slug($brand, '-');
                $slug = $baseSlug;
                $counter = 1;

                while (Produk::where('slug', $slug)->where('id', '!=', optional(Produk::where('reference', $brand)->first())->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                // Update atau buat produk
                $product = Produk::updateOrCreate(
                    ['reference' => $brand, 'provider_id' => $provider->id],
                    [
                        "nama" => $brand,
                        "developer" => $developer,
                        "kategori_id" => $kategori->id,
                        "slug" => $slug,
                        "status" => 'active',
                    ]
                );

                $product->wasRecentlyCreated ? $categoryResult['created']++ : $categoryResult['updated']++;
                $categoryResult['products']++;
            }

            $result['categories'][$categoryName] = $categoryResult;
            $result['total_categories']++;
            $result['total_products'] += $categoryResult['products'];
        }

        return [
            'status' => true,
            'message' => 'Successfully synced Digiflazz products',
            'data' => $result
        ];
    }

    public function handleOrder(Request $request)
    {

        logger()->info("Digiflazz Order: " . json_encode($request->all()));

        // validate request
        $request->validate([
            'ref_id' => 'required|string',
            'pulsa_code' => 'required|string',
            'hp' => 'required|string',
            'price' => 'required',
        ]);

        $refId = $request->ref_id;
        $naelstore = new NaelstoreController();

        $expectedSign = md5(env('DIGIFLAZZ_H2H_USERNAME') . env('DIGIFLAZZ_H2H_API_KEY') . $refId);
        if ($request->sign !== $expectedSign) {
            return response()->json([
                'data' => array_map('strval', [
                    'ref_id' => $refId,
                    'status' => '2',
                    'code' =>  $request->pulsa_code,
                    'hp' =>  $request->hp,
                    'price' =>  $request->price,
                    'message' => 'Invalid signature',
                    'balance' => '',
                    'tr_id' => '',
                    'rc' => '204',
                    'sn' => '',
                ])
            ]);
        }

        $existing = Pembelian::with('user')->where('order_id', 'LIKE', "%$refId%")->first();


        if ($existing) {

            // get balance
            $response = $naelstore->getNaelstoreBalance();
            $balance = $response['status'] ?  $response['data'] : '';


            return response()->json([
                'data' => array_map('strval', [
                    'ref_id' => $refId,
                    'status' => $existing->status === 'completed' ? '1' : ($existing->status === 'failed' ? '2' : '0'),
                    'code' =>  $existing->layanan->kode_layanan,
                    'hp' =>  $existing->input_zone ? $existing->input_id . $existing->input_zone : $existing->input_id,
                    'price' => $existing->total_price,
                    'message' => ucfirst($existing->status),
                    'balance' =>  $balance,
                    'tr_id' => $existing->order_id,
                    'rc' => $existing->status === 'completed' ? '00' : ($existing->status === 'failed' ? '07' : '39'),
                    'sn' => '',
                ])
            ]);
        }

        if ($request->commands !== 'topup') {
            return response()->json([
                'data' => array_map('strval', [
                    'ref_id' => $refId,
                    'status' => '2',
                    'code' =>  $request->pulsa_code,
                    'hp' =>  $request->hp,
                    'price' =>  $request->price,
                    'message' => 'Command not supported',
                    'balance' => '',
                    'tr_id' => '',
                    'rc' => '20',
                    'sn' => '',
                ])
            ]);
        }

        $layanan = Layanan::where('id', $request->pulsa_code)->first();
        if (!$layanan) {
            return response()->json([
                'data' => array_map('strval', [
                    'ref_id' => $refId,
                    'status' => '2',
                    'code' =>  $request->pulsa_code,
                    'hp' =>  $request->hp,
                    'price' =>  $request->price,
                    'message' => 'Product not found',
                    'balance' => '',
                    'tr_id' => '',
                    'rc' => '106',
                    'sn' => '',
                ])
            ]);
        }

        if ($request->price < $layanan->harga_beli_idr) {
            return response()->json([
                'data' => array_map('strval', [
                    'ref_id' => $refId,
                    'status' => '2',
                    'code' =>  $request->pulsa_code,
                    'hp' =>  $request->hp,
                    'price' =>  $request->price,
                    'message' => 'Price too low',
                    'balance' => '',
                    'tr_id' => '',
                    'rc' => '106',
                    'sn' => '',
                ])
            ]);
        }

        DB::beginTransaction();

        try {
            // $order_id = $this->generateUniqueOrderId();

            $params = [
                'layanan_id' => $layanan->kode_layanan,
                'quantity' => 1,
                'target' => $request->hp,
            ];

            $naelstoreProvider = Provider::where('provider_name', 'naelstore')->first();

            $client = new Client([
                'base_uri' => $naelstoreProvider->base_url,
                'headers' => [
                    'X-API-KEY' => $naelstoreProvider->api_key
                ]
            ]);

            $response = $client->request('POST', 'order', [
                'form_params' => $params
            ]);

            $apiResult = json_decode($response->getBody()->getContents(), true);

            // get balance
            $response = $naelstore->getNaelstoreBalance();
            $balance = $response['status'] ? $response['data'] : '';

            if (!$apiResult['status']) {
                DB::rollBack();
                return response()->json([
                    'data' => array_map('strval', [
                        'ref_id' => $refId,
                        'status' => '2',
                        'code' =>  $request->pulsa_code,
                        'hp' =>  $request->hp,
                        'price' =>  $request->price,
                        'message' => 'Transaction failed: ' . $apiResult['message'],
                        'balance' =>  $balance,
                        'tr_id' => '',
                        'rc' => '07',
                        'sn' => '',
                    ])
                ]);
            } else {

                // $data = $apiResult['data'];
                $pembayaran = Pembayaran::create([
                    'tipe' => 'order',
                    'order_id' => $refId,
                    'price' => $request->price,
                    'fee' => 0,
                    'total_price' => $request->price,
                    'payment_method' => 'API Digiflazz',
                    'payment_provider' => 'Saldo Akun',
                    'status' => 'paid',
                ]);


                $pembelian = Pembelian::create([
                    'order_id' => $refId,
                    'order_type' => 'game',
                    'user_id' => null,
                    'layanan_id' => $layanan->id,
                    'input_id' => $request->hp,
                    'price' => $request->price,
                    'quantity' => 1,
                    'total_price' => $request->price,
                    'profit' => $request->price - $layanan->harga_beli_idr,
                    'status' => $apiResult['status'] === true ? 'processing' : 'failed',
                    'reference_id' => $apiResult['order_id'],
                    'phone' => $request->hp,
                ]);

                DB::commit();

                return response()->json([
                    'data' => array_map('strval', [
                        'ref_id' => $refId,
                        'status' => '0',
                        'code' =>  $request->pulsa_code,
                        'hp' =>  $request->hp,
                        'price' =>  $request->price,
                        'message' => 'Process',
                        'balance' => '',
                        'tr_id' => $refId,
                        'rc' => '39',
                        'sn' => '',
                    ])
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'data' => array_map('strval', [
                    'ref_id' => $refId,
                    'status' => '2',
                    'code' =>  $request->pulsa_code,
                    'hp' =>  $request->hp,
                    'price' =>  $request->price,
                    'message' => 'Internal error: ' . $e->getMessage(),
                    'balance' => '',
                    'tr_id' => '',
                    'rc' => '07',
                    'sn' => '',
                ])
            ]);
        }
    }
}
