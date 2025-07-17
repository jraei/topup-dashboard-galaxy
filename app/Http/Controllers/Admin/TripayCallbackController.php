<?php

namespace App\Http\Controllers\Admin;


use Exception;
use App\Models\Deposit;
use App\Models\Voucher;
use App\Models\Pembelian;
use App\Models\Pembayaran;
use ZerosDev\TriPay\Client;
use Illuminate\Http\Request;
use App\Models\FlashsaleItem;
use ZerosDev\TriPay\Callback;
use App\Models\PaymentProvider;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MoogoldController;
use App\Http\Controllers\NaelstoreController;

class TripayCallbackController extends Controller
{
    protected $config;
    public function __construct()
    {
        $provider = PaymentProvider::where('provider_name', 'Tripay')->first();

        $this->config =  [
            "mode" => 'production',
            "merchant_code" => $provider['kode_merchant'],
            "api_key" => $provider['api_key'],
            "private_key" => $provider['private_key'],
            "guzzle_options" => []
        ];
    }

    public function handle()
    {
        $client = new Client($this->config);
        $callback = new Callback($client);

        // $callback->enableDebug();
        try {
            $callback->validate();
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $data = $callback->data();

        $tripayReference = $data->reference;
        $invoice = Pembayaran::where('payment_reference', $tripayReference)
            ->first();
        $order_id = $invoice->order_id;

        // return response()->json([
        //     'status' => 'error',
        //     'message' => $invoice->tipe
        // ]);

        if ($invoice->tipe == "order") {

            $dataPembelian = Pembelian::where('order_id', $order_id)->first();
            $dataLayanan = $dataPembelian->layanan;

            $dataProduk = $dataLayanan->produk;
            $quantity = $dataPembelian->quantity;

            $uid = $dataPembelian->input_id ?? null;
            $zone = $dataPembelian->input_zone ?? null;
            $kode_layanan = $dataLayanan->kode_layanan;


            if (!$invoice || $invoice->status != 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invoice not found or status not pending',
                ]);
            }

            if (!$dataPembelian || $dataPembelian->status != 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found or status not pending',
                ]);
            }

            if (intval($data->total_amount) !== (int) $invoice->total_price) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Total amount not match',
                ]);
            }

            $status = null;
            $referenceIds = [];

            if ($data->status == "PAID") {

                $invoice->update(['status' => 'paid']);

                $referenceId = null;
                $successOrders = [];
                $failedOrders = [];
                $allCompleted = true;
                $status = null;

                $provider = $dataLayanan->provider->provider_name;

                switch ($provider) {
                    case "digiflazz":
                        for ($i = 0; $i < $quantity; $i++) {
                            $subOrderId = $order_id . '-' . ($i + 1);
                            $digiflazz = new DigiflazzController();
                            $target = $uid . ($zone ? $uid : '');

                            $apiResult = $digiflazz->createTransaction([
                                'kode_layanan' => $dataLayanan->kode_layanan,
                                'target' => $target,
                                'ref_id' => $subOrderId,
                            ]);

                            if (isset($apiResult['status']) && !$apiResult['status']) {
                                return response()->json([
                                    'status' => false,
                                    'message' => $apiResult['message']
                                ], 400);
                            }


                            if (!isset($apiResult['status']) || !in_array($apiResult['status'], ["Pending", "Sukses"])) {
                                $failedOrders[] = [
                                    'order_id' => $subOrderId,
                                    'message' => $apiResult['message'] ?? "DF | Create transaction error"
                                ];
                                $allCompleted = false;
                                continue;
                            }

                            $referenceId = $order_id;
                            $successOrders[] = [
                                'status' => $apiResult['status'],
                                'reference' => $apiResult['ref_id']
                            ];
                            $status = $allCompleted ? "completed" : "failed";
                        }
                        break;
                    case "moogold":
                        $moogold = new MoogoldController;
                        $order = $moogold->createTransaction([
                            'category_id' => $dataProduk->moogold_order_category,
                            'order_id' => $order_id,
                            'service_id' => $kode_layanan,
                            'quantity' => $dataPembelian->quantity,
                            'user_id' => $uid,
                            'server' => $zone
                        ]);

                        if (!$order['data']['status']) {
                            return response()->json([
                                'status' => 'error',
                                'message' => $order['data']['raw']['err_message'] ?? $order['data']['message']
                            ]);
                        }

                        $data = $order['data'];
                        $status = in_array($data['response']['status'], ["pending", "processing"]) ? "completed" : "failed";
                        $referenceId = $data['order_id'];

                        break;
                    case "naelstore":
                        $naelstore = new NaelstoreController();
                        $target = $uid . ($zone ? '-' . $zone : '');

                        $response = $naelstore->createTransaction([
                            'layanan_id' => $kode_layanan,
                            'quantity' => $dataPembelian->quantity,
                            'target' => $target,
                        ]);

                        if (!$response['status']) {
                            return response()->json([
                                'status' => 'error',
                                'message' => $response['message']
                            ]);
                        }

                        $data = $response['data'];
                        $status = $data['status'] == true ? "completed" : "failed";

                        $referenceId = $data['order_id'];
                        break;
                    default:
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Provider not found',
                        ]);
                }


                // ✅ Update pembelian hanya jika status ditentukan
                if ($status) {

                    $references = collect($successOrders)->pluck('reference')->implode(',');

                    $dataPembelian->update([
                        'status' => $status,
                        'reference_id' => $provider == "digiflazz" ? $references : $referenceId,
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Status pembelian & pembayaran berhasil diperbarui',
                        'data' => [
                            'reference_id' => $referenceId,
                            'status' => $status,
                            'success' => $successOrders,
                            'failed' => $failedOrders
                        ]
                    ]);
                }

                return response()->json([
                    'status' => 'error',
                    'message' => 'Status tidak valid',
                ]);
            } else if ($data->status == "EXPIRED" || $data->status == "FAILED") {

                // restore voucher stock
                if ($dataPembelian->voucher_id) {
                    $voucher = Voucher::find($dataPembelian->voucher_id);
                    if ($voucher) {
                        $voucher->update([
                            'usage_count' => $voucher->usage_count - 1
                        ]);
                    }
                }

                // restore flashsale stock
                if ($dataPembelian->flashsale_item_id) {
                    $flashsaleItem = FlashsaleItem::find($dataPembelian->flashsale_item_id);
                    if ($flashsaleItem) {
                        if ($flashsaleItem->stok_tersedia != null) {
                            $flashsaleItem->update([
                                'stok_terjual' => $flashsaleItem->stok_terjual - $dataPembelian->quantity
                            ]);
                        }

                        if ($flashsaleItem->stok_tersedia != null) {
                            $flashsaleItem->update([
                                'stok_tersedia' => $flashsaleItem->stok_tersedia + $dataPembelian->quantity
                            ]);
                        }
                    }
                }



                $invoice->update(['status' => 'failed']);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment failed',
                ]);
            } else {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Status tidak valid',
                ]);
            }
        } else if ($invoice->tipe == "deposit") {


            $deposit = Deposit::where('deposit_id', $order_id)->first();

            if ($deposit->status != "pending") {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Deposit already processed',
                ]);
            }

            $user = $deposit->user;


            if ($data->status == "PAID") {
                $saldoAkhir = $user->saldo + $deposit->amount;
                $user->update(['saldo' => $saldoAkhir]);
                $deposit->update(['status' => 'paid']);
                $invoice->update(['status' => 'paid']);

                return response()->json([
                    'status' => 'success',
                    'data' => 'Sukses menambahkan saldo user',
                ]);
            } else if ($data->status == "EXPIRED" || $data->status == "FAILED") {
                $invoice->update(['status' => 'failed']);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment failed',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Status tidak valid',
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Tipe invoice tidak valid',
            ]);
        }


        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
