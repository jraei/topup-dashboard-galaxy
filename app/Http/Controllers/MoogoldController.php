<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use GuzzleHttp\Client;
use App\Models\Layanan;
use App\Models\Kategori;
use App\Models\Provider;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Medboubazine\Moogold\Moogold;
use Illuminate\Support\Facades\DB;
use Medboubazine\Moogold\Auth\Credentials;

class MoogoldController extends Controller
{
    protected $credentials;
    public function __construct()
    {
        $provider = Provider::where('provider_name', 'moogold')->first();
        $user_id = $provider->api_username;
        $partner_id = $provider->api_key;
        $secret_key = $provider->api_private_key;

        $this->credentials = new Credentials($user_id, $partner_id, $secret_key);
    }

    public function getMoogoldBalance()
    {
        try {
            $moogold =  new Moogold($this->credentials);
            $user = $moogold->user();
            $balance = $user->balance();

            return collect([
                'status' => true,
                'data' => $balance->getAmount()
            ]);
            // return $balance->getAmount();
        } catch (\Exception $e) {
            logger()->error("Failed to get balance: " . $e->getMessage());
            return  collect([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getMoogoldProducts()
    {
        $provider = Provider::where('provider_name', 'moogold')
            ->where('status', 'active')
            ->first(['id', 'provider_name']);

        if (!$provider) {
            return [
                'status' => false,
                'message' => 'Provider not found or inactive!',
            ];
        }

        $categories = Kategori::where('provider_id', $provider->id)
            ->where('status', 'active')
            ->get(['id', 'kode_kategori', 'kategori_name']);

        try {
            $moogold = new Moogold($this->credentials);
            $productsApi = $moogold->products();
        } catch (\Exception $e) {
            logger()->error("Moogold API connection failed: " . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Failed to connect to Moogold API: ' . $e->getMessage(),
            ];
        }

        $result = [
            'total_categories' => 0,
            'total_products' => 0,
            'total_created' => 0,
            'total_updated' => 0,
            'categories' => []
        ];

        // Ambil semua slug yang sudah ada untuk validasi
        $existingSlugs = Produk::pluck('slug')->toArray();

        foreach ($categories as $category) {
            $categoryResult = [
                'name' => $category->kategori_name,
                'products' => 0,
                'created' => 0,
                'updated' => 0,
                'failed' => 0
            ];

            try {
                $response = $productsApi->list($category->kode_kategori);

                if (empty($response)) {
                    continue;
                }

                $batchInsert = [];
                $batchUpdate = [];
                $now = now();

                foreach ($response as $product) {
                    $productName = $product->getName();
                    $productRef = $product->getId();

                    if ($this->containsNonLatinChars($productName)) {
                        $categoryResult['failed']++;
                        continue;
                    }

                    // Generate slug hanya berdasarkan nama produk
                    $slug = $this->generateUniqueSlug($productName, $existingSlugs);
                    $existingSlugs[] = $slug; // Tambahkan ke daftar slug yang ada

                    $productData = [
                        "nama" => $productName,
                        "developer" => 'Unknown Developer',
                        "kategori_id" => $category->id,
                        "moogold_order_category" => $category->kode_kategori == 50 ? 1 : 2,
                        "slug" => $slug,
                        "status" => 'active',
                        "provider_id" => $provider->id,
                        "reference" => $productRef,
                        "updated_at" => $now
                    ];

                    // Cek apakah produk sudah ada berdasarkan reference
                    $existingProduct = Produk::where('reference', $productRef)
                        ->where('provider_id', $provider->id)
                        ->first(['id']);

                    if ($existingProduct) {
                        $batchUpdate[] = $productData + ['id' => $existingProduct->id];
                    } else {
                        $productData['created_at'] = $now;
                        $batchInsert[] = $productData;
                    }
                }

                // Eksekusi batch insert
                if (!empty($batchInsert)) {
                    try {
                        Produk::insert($batchInsert);
                        $createdCount = count($batchInsert);
                        $categoryResult['created'] = $createdCount;
                        $categoryResult['products'] += $createdCount;
                        $result['total_created'] += $createdCount;
                        $result['total_products'] += $createdCount;
                    } catch (\Exception $e) {
                        logger()->error("Batch insert failed for category {$category->kategori_name}: " . $e->getMessage());
                        $categoryResult['failed'] += count($batchInsert);
                    }
                }

                // Eksekusi batch update
                if (!empty($batchUpdate)) {
                    $updatedCount = $this->batchUpdateProducts($batchUpdate);
                    $categoryResult['updated'] = $updatedCount;
                    $categoryResult['products'] += $updatedCount;
                    $result['total_updated'] += $updatedCount;
                    $result['total_products'] += $updatedCount;
                }
            } catch (\Exception $e) {
                logger()->error("Failed processing category {$category->kategori_name}: " . $e->getMessage());
                $categoryResult['failed']++;
                continue;
            }

            if ($categoryResult['products'] > 0) {
                $result['categories'][$category->kategori_name] = $categoryResult;
                $result['total_categories']++;
            }
        }

        return [
            'status' => true,
            'message' => 'Products synchronized successfully',
            'data' => $result
        ];
    }

    /**
     * Generate slug unik hanya berdasarkan nama produk
     */
    private function generateUniqueSlug($productName, &$existingSlugs)
    {
        $baseSlug = Str::slug($productName, '-');
        $slug = $baseSlug;
        $counter = 1;

        while (in_array($slug, $existingSlugs)) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Helper method untuk deteksi karakter non-Latin
     */
    private function containsNonLatinChars($string)
    {
        return preg_match('/[\p{Han}]/u', $string) || preg_match('/[^\x00-\x7F]/', $string);
    }

    /**
     * Batch update produk dengan single query
     */
    /**
     * Batch update produk dengan single query
     * Sesuai dengan revisi validasi slug terbaru
     */
    private function batchUpdateProducts(array $data)
    {
        if (empty($data)) {
            return 0;
        }

        $table = (new Produk())->getTable();
        $cases = [];
        $ids = [];
        $params = [];

        foreach ($data as $row) {
            $ids[] = $row['id'];

            // Untuk nama
            $cases[] = "WHEN id = ? THEN ?";
            $params[] = $row['id'];
            $params[] = $row['nama'];

            // Untuk slug
            $cases[] = "WHEN id = ? THEN ?";
            $params[] = $row['id'];
            $params[] = $row['slug'];

            // Untuk kategori_id
            $cases[] = "WHEN id = ? THEN ?";
            $params[] = $row['id'];
            $params[] = $row['kategori_id'];

            // Untuk moogold_order_category
            $cases[] = "WHEN id = ? THEN ?";
            $params[] = $row['id'];
            $params[] = $row['moogold_order_category'];
        }

        $idsStr = implode(',', $ids);
        $casesStr = implode(' ', $cases);

        $query = "UPDATE {$table} SET 
        nama = CASE {$casesStr} END,
        slug = CASE {$casesStr} END,
        kategori_id = CASE {$casesStr} END,
        moogold_order_category = CASE {$casesStr} END,
        updated_at = NOW()
        WHERE id IN ({$idsStr})";

        try {
            return DB::update($query, $params);
        } catch (\Exception $e) {
            logger()->error("Batch update products failed: " . $e->getMessage());
            return 0;
        }
    }


    public function getMoogoldServices()
    {
        $provider = Provider::where('provider_name', 'moogold')->first();
        if (!$provider || $provider->status != 'active') {
            return [
                'status' => false,
                'message' => 'Provider not found or inactive!'
            ];
        }

        $moogold = new Moogold($this->credentials);
        $moogoldProducts = $moogold->products();
        $activeProducts = Produk::where('provider_id', $provider->id)
            ->where('status', 'active')
            ->get(['id', 'reference', 'thumbnail', 'nama']);

        $affectedRows = [
            'success' => 0,
            'failed' => 0,
            'updated' => 0,
            'created' => 0
        ];

        // Preload semua layanan yang ada untuk produk aktif
        $existingServices = Layanan::whereIn('produk_id', $activeProducts->pluck('id'))
            ->get(['id', 'produk_id', 'kode_layanan', 'harga_beli'])
            ->keyBy('kode_layanan');

        foreach ($activeProducts as $produk) {
            try {
                $response = $moogoldProducts->details($produk['reference']);

                if (empty($response)) {
                    continue;
                }

                $productName = $response->getName();

                // Update thumbnail produk jika kosong
                if (empty($produk['thumbnail']) && !empty($response->getImageUrl())) {
                    $produk->update(['thumbnail' => $response->getImageUrl()]);
                }

                $services = $response->getOffers();
                $batchInsert = [];
                $batchUpdate = [];

                foreach ($services as $data) {
                    $dataId = $data->getId();
                    $price = $data->getPrice();

                    // Validasi data
                    if (empty($dataId) || !is_scalar($dataId) || !is_numeric($price)) {
                        logger()->error("Invalid data - ID: " . json_encode($dataId) . ", Price: " . json_encode($price));
                        $affectedRows['failed']++;
                        continue;
                    }

                    // Proses nama layanan
                    $serviceName = $this->cleanServiceName($data->getName(), $productName, $dataId);

                    // Cek apakah layanan sudah ada
                    if ($existingServices->has($dataId)) {
                        $existingService = $existingServices->get($dataId);

                        // Hanya update jika harga berbeda
                        if ($existingService->harga_beli != $price) {
                            $batchUpdate[] = [
                                'id' => $existingService->id,
                                'harga_beli' => $price,
                                'status' => 'active',
                                'updated_at' => now()
                            ];
                            $affectedRows['updated']++;
                        }
                    } else {
                        // Tambahkan ke batch insert
                        $batchInsert[] = [
                            'produk_id' => $produk->id,
                            'provider_id' => $provider->id,
                            'kode_layanan' => $dataId,
                            'nama_layanan' => $serviceName,
                            'harga_beli' => $price,
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $affectedRows['created']++;
                    }

                    $affectedRows['success']++;
                }

                // Eksekusi batch update
                if (!empty($batchUpdate)) {
                    $this->batchUpdateLayanan($batchUpdate);
                }

                // Eksekusi batch insert
                if (!empty($batchInsert)) {
                    Layanan::insert($batchInsert);
                    // Tambahkan ke existing services untuk menghindari duplicate insert
                    foreach ($batchInsert as $newService) {
                        $existingServices->put($newService['kode_layanan'], (object)[
                            'id' => null, // Tidak perlu ID untuk pengecekan berikutnya
                            'produk_id' => $newService['produk_id'],
                            'kode_layanan' => $newService['kode_layanan'],
                            'harga_beli' => $newService['harga_beli']
                        ]);
                    }
                }
            } catch (\Exception $e) {
                logger()->error("Failed to fetch layanan for product {$produk->nama}: " . $e->getMessage());
                $affectedRows['failed']++;
            }
        }

        return [
            'status' => true,
            'message' => 'Services synchronized successfully',
            'data' => [
                'affected_rows' => $affectedRows['success'],
                'updated_services' => $affectedRows['updated'],
                'created_services' => $affectedRows['created'],
                'failed_services' => $affectedRows['failed']
            ]
        ];
    }

    /**
     * Bersihkan nama layanan
     */
    protected function cleanServiceName($rawName, $productName, $dataId)
    {
        $serviceName = $rawName;

        // Hapus ID tag
        $idTag = "(#{$dataId})";
        if (strpos($serviceName, $idTag) !== false) {
            $serviceName = str_replace($idTag, '', $serviceName);
        }

        // Hapus nama produk dari awal nama layanan
        $productNameClean = trim($productName);
        if (stripos($serviceName, $productNameClean) === 0) {
            $serviceName = trim(str_replace($productNameClean, '', $serviceName));
            $serviceName = preg_replace('/^\s*-\s*/', '', $serviceName);
        }

        return trim($serviceName) ?: $rawName; // Fallback ke nama original jika kosong
    }

    /**
     * Batch update layanan
     */
    protected function batchUpdateLayanan(array $data)
    {
        $table = (new Layanan())->getTable();
        $cases = [];
        $ids = [];
        $params = [];

        foreach ($data as $row) {
            $ids[] = $row['id'];
            $cases[] = "WHEN {$row['id']} THEN ?";
            $params[] = $row['harga_beli'];
        }

        $ids = implode(',', $ids);
        $cases = implode(' ', $cases);

        return DB::update("UPDATE {$table} SET 
        harga_beli = CASE id {$cases} END,
        status = 'active',
        updated_at = NOW()
        WHERE id IN ({$ids})", $params);
    }

    public function createTransaction(array $data)
    {
        try {
            $result = $this->createTransactionRequest(
                $data['category_id'],
                $data['order_id'],
                $data['service_id'],
                $data['quantity'],
                $data['user_id'] ?? null,
                $data['server'] ?? null
            );

            return collect([
                'status' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            logger()->error("Failed to create transaction: " . $e->getMessage());
            return collect([
                'status' => false,
                'data' => $e->getMessage()
            ]);
        }
    }



    public function createTransactionRequest(int $categoryType, string $externalId, int $offerId, int $quantity, ?string $user_id = null, ?string $server = null)
    {
        // Setup info dasar
        $timestamp = time(); // Unix timestamp
        $path = "order/create_order"; // Endpoint path dari Moogold
        $baseUri = "https://moogold.com/wp-json/v1/api/";

        // Credentials (replace ini sesuai object atau .env kamu)
        $partnerId = $this->credentials->partner_id;
        $secretKey = $this->credentials->secret_key;

        // Bangun body untuk API
        $data = [
            "category" => $categoryType,
            "product-id" => $offerId,
            "quantity" => $quantity,
        ];
        // Tambahkan jika ada
        if ($user_id !== null) {
            $data["User ID"] = $user_id;
        }
        if ($server !== null) {
            $data["Server ID"] = $server;
        }

        $bodyArray = [
            "path" => $path,
            "data" => $data,
            "partnerOrderId" => $externalId,
        ];

        $body = json_encode($bodyArray);
        // Signature
        $signature = hash_hmac('SHA256', "{$body}{$timestamp}{$path}", $secretKey);

        // Headers
        $headers = [
            "Accept" => "application/json",
            "timestamp" => $timestamp,
            "auth" => $signature,
            "Authorization" => "Basic " . base64_encode("{$partnerId}:{$secretKey}"),
        ];

        // Guzzle request
        $client = new Client([
            "base_uri" => $baseUri,
            "timeout"  => 10,
            "allow_redirects" => false,
            "http_errors" => false,
            "verify" => true,
            "headers" => $headers,
        ]);

        try {
            $response = $client->request("POST", $path, [
                "body" => $body
            ]);

            if ($response->getStatusCode() === 200) {
                $contents = $response->getBody();
                $data = json_decode($contents, true);

                if (isset($data['status']) && isset($data['order_id'])) {
                    return [
                        'status' => true,
                        'order_id' => $data['order_id'],
                        'response' => $data,
                    ];
                } else {
                    return [
                        'status' => false,
                        'message' => 'Invalid response structure',
                        'raw' => $data,
                    ];
                }
            } else {
                return [
                    'status' => false,
                    'message' => "HTTP Error: " . $response->getStatusCode(),
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => "Exception: " . $e->getMessage(),
            ];
        }
    }
}