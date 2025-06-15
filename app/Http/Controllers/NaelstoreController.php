<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Produk;
use GuzzleHttp\Client;
use App\Models\Layanan;
use App\Models\Kategori;
use App\Models\Provider;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\RequestException;

class NaelstoreController extends Controller
{

    private $client, $naelstore;

    public function __construct()
    {
        $this->naelstore = Provider::where('provider_name', 'naelstore')->first();
        $this->client = new Client([
            'base_uri' => $this->naelstore->base_url,
            'headers' => [
                'X-API-KEY' => $this->naelstore->api_key
            ]
        ]);
    }

    public function getNaelstoreBalance()
    {
        try {
            $response = $this->client->request('POST', 'balance');

            $data = json_decode($response->getBody()->getContents());

            $balance = $data->balance;

            return collect([
                'status' => true,
                'data' => $balance
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

    public function getNaelstoreProduk()
    {
        try {
            $response = $this->client->request('POST', 'products');
            $data = json_decode($response->getBody()->getContents());

            $products = $data->status ? $data->products : [];

            if (empty($products)) {
                return collect([
                    'status' => false,
                    'message' => 'No product data found.'
                ]);
            }

            // 1. Ambil semua kategori unik
            $kategoriList = collect($products)
                ->pluck('kategori.kategori_name')
                ->unique()
                ->filter()
                ->values();

            // 2. Insert kategori jika belum ada
            $kategoriMap = [];
            foreach ($kategoriList as $kategoriName) {
                $kategori = Kategori::firstOrCreate(
                    ['kategori_name' => $kategoriName],
                    ['status' => 'active'],
                    ['provider_id' => $this->naelstore->id]
                );
                $kategoriMap[$kategoriName] = $kategori->id;
            }

            // 3. Insert produk berdasarkan kategori
            $created = 0;
            $updated = 0;

            foreach ($products as $item) {
                if (empty($item->nama) || empty($item->kategori?->kategori_name)) continue;

                $kategoriName = $item->kategori->kategori_name;
                $kategoriId = $kategoriMap[$kategoriName] ?? null;

                if (!$kategoriId) continue;

                // Generate slug
                $baseSlug = Str::slug($item->nama);
                $slug = $baseSlug;
                $counter = 1;

                while (Produk::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                $product = Produk::updateOrCreate(
                    ['id' => $item->id],
                    [
                        'provider_id' => $this->naelstore->id,
                        'nama' => $item->nama,
                        'kategori_id' => $kategoriId,
                        'slug' => $slug,
                        'status' => 'active',
                    ]
                );

                $product->wasRecentlyCreated ? $created++ : $updated++;
            }

            return collect([
                'status' => true,
                'message' => 'Produk berhasil di-sync',
                'data' => [
                    'created' => $created,
                    'updated' => $updated,
                    'total_products' => $created + $updated,
                    'categories' => $kategoriList->count()
                ]
            ]);
        } catch (\Exception $e) {
            logger()->error("Failed to get product list: " . $e->getMessage());
            return collect([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getNaelstoreServices()
    {
        $provider = Provider::where('provider_name', 'naelstore')->first();

        if (!$provider || $provider->status != 'active') {
            return [
                'status' => false,
                'message' => 'Provider Naelstore tidak ditemukan atau tidak aktif.'
            ];
        }

        try {
            $response = $this->client->request('POST', 'services');
            $json = json_decode($response->getBody()->getContents());
        } catch (\Exception $e) {
            logger()->error("Gagal mengambil data layanan dari Naelstore: " . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error saat mengambil data dari API: ' . $e->getMessage()
            ];
        }

        $services = $json->status ? $json->services : [];

        // Ambil produk yang aktif dan punya provider ini
        $products = Produk::where('provider_id', $provider->id)
            ->where('status', 'active')
            ->pluck('id', 'nama')
            ->mapWithKeys(fn($id, $name) => [Str::upper($name) => $id]);

        // Ambil layanan yang sudah ada untuk update
        $existingServices = Layanan::where('provider_id', $provider->id)
            ->where('status', 'active')
            ->pluck('id', 'kode_layanan')
            ->toArray();

        $now = now();
        $batchInsert = [];
        $batchUpdate = [];

        foreach ($services as $service) {
            $kode = $service->id ?? null;
            $nama = $service->nama ?? null;
            $productName = $service->produk->nama ?? null;

            if (!$kode || !$nama) continue;

            $produkId = $products[Str::upper($productName)] ?? null;
            if (!$produkId) continue;

            $harga = $service->harga ?? 0;
            $hargaIDR = is_numeric($harga) ? round($harga) : 0;

            $payload = [
                'produk_id' => $produkId,
                'provider_id' => $provider->id,
                'kode_layanan' => $kode,
                'nama_layanan' => $nama,
                'gambar' => null,
                'harga_beli' => $harga,
                'harga_beli_idr' => $hargaIDR,
                'catatan' => null,
                'status' => 'active',
                'updated_at' => $now
            ];

            if (isset($existingServices[$kode])) {
                $payload['id'] = $existingServices[$kode];
                $batchUpdate[] = $payload;
            } else {
                $payload['created_at'] = $now;
                $batchInsert[] = $payload;
            }
        }

        // Proses batch update dan insert
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
            'message' => 'Layanan dari Naelstore berhasil disinkron.',
            'data' => [
                'total_services' => count($services),
                'updated' => count($batchUpdate),
                'created' => count($batchInsert),
                'affected_rows' => $affectedRows
            ]
        ];
    }

    private function batchUpdateLayanan(array $data): int
    {
        if (empty($data)) return 0;

        $table = (new \App\Models\Layanan())->getTable(); // 'layanans'
        $cases = [];
        $ids = [];
        $columns = [
            'produk_id',
            'provider_id',
            'nama_layanan',
            'gambar',
            'harga_beli',
            'harga_beli_idr',
            'catatan',
            'status',
            'updated_at'
        ];

        foreach ($columns as $column) {
            $cases[$column] = "CASE id\n";
        }

        foreach ($data as $row) {
            $id = (int) $row['id'];
            $ids[] = $id;

            foreach ($columns as $column) {
                $value = is_null($row[$column]) ? 'NULL' : DB::getPdo()->quote($row[$column]);
                $cases[$column] .= "WHEN {$id} THEN {$value}\n";
            }
        }

        foreach ($columns as $column) {
            $cases[$column] .= "END";
        }

        $updateQuery = "UPDATE `{$table}` SET ";
        $updateQuery .= implode(", ", array_map(fn($col) => "`$col` = {$cases[$col]}", $columns));
        $updateQuery .= " WHERE id IN (" . implode(",", $ids) . ")";

        return DB::update($updateQuery);
    }



    public function createTransaction(array $data)
    {

        // validate
        $validator = Validator::make($data, [
            'layanan_id' => 'required|numeric',
            'target' => 'required|string',
            'quantity' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return collect([
                'status' => false,
                'message' => 'Validation failed: ' . $validator->errors(),
            ]);
        }

        // try catch
        try {
            $params = [
                'layanan_id' => $data['layanan_id'],
                'target' => $data['target'],
                'quantity' => $data['quantity'],
            ];

            $response = $this->client->request('POST', 'order', [
                'form_params' => $params
            ]);

            $data = json_decode($response->getBody()->getContents());

            return collect([
                'status' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            logger()->error("Failed to create transaction: " . $e->getMessage());
            return collect([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }



    /**
     * Parse API error response
     */
    protected function parseApiError(RequestException $e)
    {
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            try {
                $errorData = json_decode($response->getBody()->getContents());
                return $errorData->message ?? $response->getReasonPhrase();
            } catch (\Exception $jsonError) {
                return $response->getReasonPhrase();
            }
        }

        return $e->getMessage();
    }
}