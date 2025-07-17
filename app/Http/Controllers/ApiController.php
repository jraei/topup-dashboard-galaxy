<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Produk;
use App\Models\Layanan;
use App\Models\Pembelian;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admin\DigiflazzController;

class ApiController extends Controller
{

    public function docs()
    {
        return Inertia::render('ApiDocs');
    }

    public function updateIpWhitelist(Request $request)
    {
        $request->validate([
            'ip_whitelist' => 'nullable|string'
        ]);

        $user = $request->user();
        $user->ip_whitelist = $request->ip_whitelist;
        $user->save();

        return response()->json(['status' => 'success']);
    }


    public function balance(Request $request): JsonResponse
    {
        $apiKey = $request->header('X-API-KEY');

        // Cari user berdasarkan api_key
        $user = User::where('api_key', $apiKey)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'API Key tidak valid'
            ], 401);
        }

        // Misal saldo ada di kolom 'balance' user
        return response()->json([
            'status' => true,
            'message' => 'Saldo berhasil didapatkan',
            'balance' => $user->saldo,
            'currency' => 'IDR'
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        // Validasi pakai Validator manual
        $validator = Validator::make($request->all(), [
            'kategori' => 'nullable|exists:kategoris,kategori_name',
        ], [
            'kategori.exists' => 'Kategori yang diminta tidak ditemukan.'
        ]);

        if ($validator->fails()) {
            // Return response JSON kalau validasi gagal
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Query produk
        $query = Produk::where('status', 'active')
            ->with('kategori:id,kategori_name')
            ->select('id', 'nama', 'kategori_id');

        if ($request->filled('kategori')) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('kategori_name', $request->kategori);
            });
        }

        $products = $query->orderBy('id')->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'nama' => $product->nama,
                'kategori' => $product->kategori,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil didapatkan',
            'products' => $products
        ]);
    }

    public function services(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'produk_id' => 'nullable|exists:produks,id',
        ], [
            'produk_id.exists' => 'Produk tidak ditemukan'
        ]);

        if ($validator->fails()) {
            // Return response JSON kalau validasi gagal
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $apiKey = $request->header('X-API-KEY');
        $user = User::where('api_key', $apiKey)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'API key tidak valid.'
            ], 401);
        }

        $userRoleId = $user->user_role_id;

        // Tetap select harga_beli_idr supaya harga_jual bisa dihitung
        $query = Layanan::where('status', 'active')->whereHas('produk', function ($q) use ($request) {
            $q->where('status', 'active');
        })
            ->with('produk:id,nama')
            ->select('id', 'nama_layanan', 'produk_id', 'harga_beli_idr');

        if ($request->filled('produk_id')) {
            $query->where('produk_id', $request->produk_id);
        }

        $layanans = $query->orderBy('harga_beli_idr')->get()->map(function ($layanan) use ($userRoleId) {
            // Hitung harga_jual
            $hargaJual = $layanan->getHargaLayanan($userRoleId);

            // Susun data dengan key custom
            return [
                'id' => $layanan->id,
                'nama' => $layanan->nama_layanan,
                'produk' => $layanan->produk,
                'harga' => $hargaJual,
            ];
        });

        if ($layanans->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Data layanan kosong'
            ], 404);
        }


        return response()->json([
            'status' => true,
            'message' => 'Data layanan berhasil diambil',
            'services' => $layanans
        ]);
    }


    public function order(Request $request)
    {
        // Validasi awal
        $validator = Validator::make($request->all(), [
            'layanan_id' => 'required|exists:layanans,id',
            'quantity' => 'required|integer|min:1',
            'target' => 'required|string',
        ], [
            'layanan_id.exists' => 'Layanan yang diminta tidak ditemukan.',
            'quantity.min' => 'Jumlah layanan minimal adalah 1',
            'target.required' => 'Target harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $apiKey = $request->header('X-API-KEY');

        // Cek API Key
        $user = User::where('api_key', $apiKey)->first();
        if (!$user || $user->status != 'active') {
            return response()->json(['status' => false, 'message' => 'Invalid or inactive API key'], 403);
        }

        // Ambil layanan & produk
        $layanan = Layanan::with('produk.provider')->findOrFail($request->layanan_id);
        $produk = $layanan->produk;
        $providerName = strtolower($produk->provider->provider_name);

        // Validasi harga jual
        if ($layanan->harga_jual <= $layanan->harga_beli_idr) {
            return response()->json(['status' => false, 'message' => 'Harga jual tidak valid'], 400);
        }

        $quantity = $request->quantity;
        $totalHargaJual = $layanan->harga_jual * $quantity;

        // Cek saldo cukup
        if ($user->saldo < $totalHargaJual) {
            return response()->json(['status' => false, 'message' => 'Saldo tidak mencukupi'], 400);
        }

        // Proses target
        $input_id = $request->target;
        $input_zone = null;

        if (strpos($request->target, '-') !== false) {
            [$idPart, $zonePart] = explode('-', $request->target, 2);
            $idPart = trim($idPart);
            $zonePart = trim($zonePart);

            if (!$idPart || !$zonePart) {
                return response()->json(['status' => false, 'message' => 'Format target tidak valid. Contoh: id-server'], 422);
            }

            $input_id = $idPart;
            $input_zone = $zonePart;
        }

        // Cek duplicate order
        $existingOrder = Pembelian::where('layanan_id', $layanan->id)
            ->where('input_id', $input_id)
            ->where('status', 'pending')
            ->first();
        if ($existingOrder) {
            return response()->json(['status' => false, 'message' => 'Pesanan serupa sedang diproses'], 409);
        }

        // Generate order ID utama
        $order_id =  $this->generateUniqueOrderId("NAPI");
        $successOrders = [];
        $failedOrders = [];
        $allCompleted = true;
        $referenceIds = [];

        // Proses berbeda berdasarkan provider
        if ($providerName == 'moogold') {
            // Moogold bisa handle quantity dalam 1 request
            $moogold = new MoogoldController();
            $apiResult = $moogold->createTransaction([
                'category_id' => $produk->moogold_order_category,
                'order_id' => $order_id,
                'service_id' => $layanan->kode_layanan,
                'quantity' => $quantity,
                'user_id' => $input_id,
            ]);

            if (!$apiResult['data']['status']) {
                $failedOrders[] = [
                    'order_id' => $order_id,
                    'message' => $apiResult['data']['raw']['err_message'] ?? $apiResult['data']['message']
                ];
                $allCompleted = false;
            } else {
                $data = $apiResult['data'];
                $status = ($data['response']['status'] == "pending" || $data['response']['status'] == "processing") ? "processing" : "failed";
                $referenceIds[] = $data['order_id'];
                $successOrders[] = [
                    'reference' => $data['order_id'],
                    'status' => $status
                ];
                $allCompleted = $allCompleted && ($status == "processing");
            }
        } elseif ($providerName == 'digiflazz') {
            // Digiflazz perlu loop per quantity
            for ($i = 0; $i < $quantity; $i++) {
                $subOrderId = $order_id . '-' . ($i + 1);
                $digiflazz = new DigiflazzController();
                $apiResult = $digiflazz->createTransaction([
                    'kode_layanan' => $layanan->kode_layanan,
                    'target' => $input_zone ? $input_id . '-' . $input_zone : $input_id,
                    'ref_id' => $subOrderId,
                ]);

                if (!isset($apiResult['status']) || !in_array($apiResult['status'], ["Pending", "Sukses"])) {
                    $failedOrders[] = [
                        'order_id' => $subOrderId,
                        'message' => $apiResult['message'] ?? "DF | Create transaction error"
                    ];
                    $allCompleted = false;
                    continue;
                }

                $status = ($apiResult['status'] == "Pending" || $apiResult['status'] == "Sukses") ? "processing" : "failed";
                $referenceIds[] = $apiResult['ref_id'];
                $successOrders[] = [
                    'reference' => $apiResult['ref_id'],
                    'status' => $status
                ];
                $allCompleted = $allCompleted && ($status == "processing");
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Provider tidak dikenali'
            ], 400);
        }

        // Jika ada yang gagal dan tidak ada yang berhasil, langsung return
        if (empty($successOrders) && !empty($failedOrders)) {
            return response()->json([
                'status' => false,
                'message' => 'Semua order gagal',
                'failed_orders' => $failedOrders,
            ], 400);
        }

        // Potong saldo 
        $user->decrement('saldo', $totalHargaJual);

        // Catat pembayaran
        Pembayaran::create([
            'tipe' => 'order',
            'order_id' => $order_id,
            'price' => $totalHargaJual,
            'fee' => 0,
            'total_price' => $totalHargaJual,
            'payment_method' => 'Saldo Akun',
            'payment_provider' => 'Saldo Akun',
            'status' => 'paid',
        ]);

        // Catat pembelian utama
        $pembelian = Pembelian::create([
            'order_id' => $order_id,
            'order_type' => 'game',
            'user_id' => $user->id,
            'layanan_id' => $layanan->id,
            'input_id' => $input_id,
            'input_zone' => $input_zone,
            'price' => $layanan->harga_jual,
            'quantity' => $quantity,
            'discount' => 0,
            'total_price' => $totalHargaJual,
            'profit' => ($layanan->harga_jual - $layanan->harga_beli_idr) * $quantity,
            'status' => 'processing',
            'reference_id' => implode(',', $referenceIds),
            'phone' => $request->phone ?? null,
            'email' => $request->email ?? null,
            'callback_data' => $providerName == 'digiflazz' ? ['sub_orders' => $successOrders] : null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order diproses',
            'order_id' => $order_id,
            // 'success_orders' => $successOrders,
            'failed_orders' => $failedOrders,
        ]);
    }


    public function checkOrderStatus(Request $request)
    {
        // Validasi input
        $request->validate([
            'order_id' => 'required|string'
        ]);

        // Cari pembelian berdasarkan order_id
        $pembelian = Pembelian::where('order_id', $request->order_id)->first();

        if (!$pembelian) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID Order tidak ditemukan'
            ], 404);
        }

        // Kirim status pembelian
        return response()->json([
            'status' => 'success',
            'message' => 'Status order sukses diambil',
            'data' => [
                'order_id' => $pembelian->order_id,
                'status' => $pembelian->status
            ]
        ]);
    }



    private function generateUniqueOrderId($prefix = null)
    {
        do {
            $timestamp = now()->format('dmHis'); // d=day, m=month, H=hour, i=minute, s=second
            $random = mt_rand(100, 999); // random 3 digit
            $orderId = $prefix . $timestamp . $random;
        } while ($this->orderIdExists($orderId));

        return $orderId;
    }

    private function orderIdExists($orderId)
    {
        return Pembelian::where('order_id', $orderId)->exists();
    }
}
