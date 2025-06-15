<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Produk;
use App\Models\Layanan;
use App\Models\Kategori;
use App\Models\Provider;
use Illuminate\Support\Str;
use Gonon\Digiflazz\Balance;
use Illuminate\Http\Request;
use Gonon\Digiflazz\Digiflazz;
use Gonon\Digiflazz\PriceList;
use Gonon\Digiflazz\Transaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Gonon\Digiflazz\Exceptions\ApiException;

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

            return $result;
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
                ['kategori_name' => $categoryName, 'provider_id' => $provider->id],
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
}