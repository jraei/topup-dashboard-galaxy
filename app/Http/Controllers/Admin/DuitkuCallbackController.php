<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produk;
use App\Models\Deposit;
use App\Models\PayMethod;
use App\Models\Pembelian;
use App\Models\WebConfig;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Models\PaymentProvider;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MoogoldController;
use App\Http\Controllers\NaelstoreController;
use App\Http\Controllers\WhatsappNotifController;

class DuitkuCallbackController extends Controller
{
    public function handle(Request $request)
    {
        try {
            Log::info('[DUITKU CALLBACK] Incoming Request: ', $request->all());

            $requiredFields = ['merchantCode', 'amount', 'merchantOrderId', 'signature', 'resultCode'];
            foreach ($requiredFields as $field) {
                if (!$request->has($field)) {
                    return response()->json(['success' => false, 'message' => "Missing field: $field"], 400);
                }
            }

            $provider = PaymentProvider::where('provider_name', 'Duitku')->first();
            $apiKey = $provider->api_key;
            $expectedMerchantCode = $provider->kode_merchant;

            if (!$apiKey || !$expectedMerchantCode) {
                return response()->json(['success' => false, 'message' => 'Duitku config incomplete'], 500);
            }

            $calcSignature = md5($request->merchantCode . $request->amount . $request->merchantOrderId . $apiKey);
            if ($request->signature !== $calcSignature) {
                return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
            }

            if ($request->merchantCode !== $expectedMerchantCode) {
                return response()->json(['success' => false, 'message' => 'Invalid merchant code'], 403);
            }

            $invoice = Pembayaran::where('payment_reference', $request->merchantOrderId)->first();
            if (!$invoice) {
                return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
            }

            $order_id = $invoice->order_id;
            if ($invoice->tipe === 'order') {
                $order = Pembelian::where('order_id', $order_id)->first();


                if (!$order || $order->status !== 'pending') {
                    return response()->json(['success' => false, 'message' => 'Order not found or not pending'], 400);
                }

                if ((int)$request->amount !== (int)$invoice->total_price) {
                    return response()->json(['success' => false, 'message' => 'Amount mismatch'], 400);
                }

                if ($request->resultCode === '00') {
                    $invoice->update(['status' => 'paid']);

                    $provider = $order->layanan->provider->provider_name;
                    $target = $order->input_id . ($order->input_zone ?? '');
                    $quantity = $order->quantity;
                    $status = null;
                    $referenceId = null;

                    switch ($provider) {
                        case 'digiflazz':
                            $digiflazz = new DigiflazzController();
                            $success = [];
                            $failed = [];
                            for ($i = 0; $i < $quantity; $i++) {
                                $ref = $order_id . '-' . ($i + 1);
                                $res = $digiflazz->createTransaction([
                                    'kode_layanan' => $order->layanan->kode_layanan,
                                    'target' => $target,
                                    'ref_id' => $ref,
                                ]);

                                if (isset($res['status']) && in_array($res['status'], ['Pending', 'Sukses'])) {
                                    $success[] = $ref;
                                } else {
                                    $failed[] = $ref;
                                }
                            }

                            $order->update([
                                'status' => count($failed) === 0 ? 'processing' : 'failed',
                                'reference_id' => implode(',', $success),
                            ]);
                            break;

                        case 'moogold':
                            $moogold = new MoogoldController();
                            $res = $moogold->createTransaction([
                                'category_id' => $order->layanan->produk->moogold_order_category,
                                'order_id' => $order_id,
                                'service_id' => $order->layanan->kode_layanan,
                                'quantity' => $quantity,
                                'user_id' => $order->input_id,
                                'server' => $order->input_zone
                            ]);

                            $status = in_array($res['data']['response']['status'], ['pending', 'processing']) ? 'processing' : 'failed';
                            $referenceId = $res['data']['order_id'];
                            $order->update(['status' => $status, 'reference_id' => $referenceId]);
                            break;

                        case 'naelstore':
                            $naelstore = new NaelstoreController();
                            $res = $naelstore->createTransaction([
                                'layanan_id' => $order->layanan->kode_layanan,
                                'quantity' => $quantity,
                                'target' => $target,
                            ]);

                            $status = $res['data']['status'] ? 'processing' : 'failed';
                            $order->update(['status' => $status, 'reference_id' => $res['data']['order_id']]);
                            break;
                    }

                    if ($status == "processing") {
                        // send notifikasi whatsapp ke nomor wa customer
                        $dataLayanan = $order->layanan;
                        $dataPembayaran = Pembayaran::where('order_id', $order_id)->first();
                        $produk = Produk::where('id', $dataLayanan->produk_id)->first();
                        $user = $order->user->name;

                        $phone = str_replace('+', '', $order->phone);
                        $produk = $produk->nama . ' - ' . $dataLayanan->nama_layanan;
                        $price = $dataPembayaran->total_price;
                        $payMethod = PayMethod::where('kode', $dataPembayaran->payment_method)->first()->nama;
                        $judulWeb = WebConfig::where('key', 'judul_web')->first()->value;

                        $whatsappNotif = new WhatsappNotifController();
                        $res = $whatsappNotif->sendMessage($phone, '*⚡Pembayaran diterima, Pesanan sedang diproses*

*Produk* : ' . $produk . '
*Order ID* : ' . $order_id . '
*Target* : ' . $target . '
*Total* : Rp ' . $price . '
*Metode Pembayaran* : ' . $payMethod . '
*Status* : ' . $status . '

*Terimakasih kak ' . $user . ' telah membeli di ' . $judulWeb ?? env('APP_NAME') . '😇*');


                        $result = json_decode($res);
                        if ($result->statusCode == 200) {
                            logger()->info("Success to send whatsapp notif [CALLBACK TRANSAKSI]: " . $result->message);
                        } else {
                            logger()->error("Failed to send whatsapp notif [CALLBACK TRANSAKSI]: " . $result->message);
                        }
                    }

                    return response()->json(['success' => true, 'message' => 'Order processed']);
                } else {
                    $invoice->update(['status' => 'failed']);
                    $order->update(['status' => 'failed']);
                    return response()->json(['success' => false, 'message' => 'Payment failed']);
                }
            } elseif ($invoice->tipe === 'deposit') {
                $deposit = Deposit::where('deposit_id', $order_id)->first();
                if (!$deposit || $deposit->status !== 'pending') {
                    return response()->json(['success' => false, 'message' => 'Deposit not found or already processed']);
                }

                if ($request->resultCode === '00') {
                    $user = $deposit->user;
                    $user->update(['saldo' => $user->saldo + $deposit->amount]);
                    $deposit->update(['status' => 'paid']);
                    $invoice->update(['status' => 'paid']);
                    return response()->json(['success' => true, 'message' => 'Saldo berhasil ditambahkan']);
                } else {
                    $deposit->update(['status' => 'failed']);
                    $invoice->update(['status' => 'failed']);
                    return response()->json(['success' => false, 'message' => 'Payment failed']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid invoice type']);
            }
        } catch (\Throwable $e) {
            Log::error('[DUITKU CALLBACK] Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error occurred'], 500);
        }
    }
}
