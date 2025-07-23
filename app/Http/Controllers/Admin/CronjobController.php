<?php

namespace App\Http\Controllers\Admin;

use GuzzleHttp\Client;

use App\Models\Provider;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Medboubazine\Moogold\Moogold;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Medboubazine\Moogold\Auth\Credentials;
use App\Http\Controllers\MoogoldController;
use App\Http\Controllers\Admin\ProviderController;

class CronjobController extends Controller
{
    public function getMoogold()
    {
        $moogoldController = new MoogoldController();

        // Sinkronisasi produk terlebih dahulu
        // $productsResult = $moogoldController->getMoogoldProducts();

        // Sinkronisasi layanan
        $servicesResult = $moogoldController->getMoogoldServices();

        // Format hasil
        $result = [
            'status' => true,
            'timestamp' => now()->toDateTimeString(),
            // 'products' => [
            //    'success' => $productsResult['success'] ?? 0,
            //    'failed' => $productsResult['failed'] ?? 0,
            //    'message' => isset($productsResult['message']) ? $productsResult['message'] : 'Product sync completed'
            // ],
            'services' => [
                'success' => $servicesResult['success'] ?? 0,
                'failed' => $servicesResult['failed'] ?? 0,
                'updated' => $servicesResult['updated'] ?? 0,
                'created' => $servicesResult['created'] ?? 0,
                'message' => isset($servicesResult['message']) ? $servicesResult['message'] : 'Service sync completed'
            ]
        ];

        // Log hasil
        // Log::channel('moogold_sync')->info('Moogold sync result', $result);

        // Return dalam format JSON
        return response()->json($result);
    }

    public function getDigiflazz()
    {
        try {
            $digiflazz = new DigiflazzController();

            $affectedProduk = $digiflazz->getDigiflazzProduk();
            $affectedLayanan = $digiflazz->getDigiflazzService();

            if (!$affectedProduk['game'] || !$affectedProduk['pulsa']) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed get products from Digiflazz: ' . $affectedProduk['message'],
                ]);
            }

            if (!$affectedLayanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed get services from Digiflazz' . $affectedLayanan['message'],
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Success get services & products from Digiflazz',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function statusMoogold()
    {
        $pendingOrders = Pembelian::whereIn('status', ['pending', 'processing'])->whereHas('layanan.provider', function ($q) {
            $q->where('provider_name', 'moogold');
        })->get();

        $provider = Provider::where('provider_name', 'moogold')->first();
        $user_id = $provider->api_username;
        $partner_id = $provider->api_key;
        $secret_key = $provider->api_private_key;

        $credentials = new Credentials($user_id, $partner_id, $secret_key);

        $moogold = new Moogold($credentials);

        $log = [
            'success_updates' => [],
            'errors' => [],
            'unchanged' => [],
        ];

        foreach ($pendingOrders as $order) {
            $referenceIds = explode(',', $order->reference_id);
            $statuses = [];

            foreach ($referenceIds as $refId) {
                try {
                    // turn refId into int
                    $refId = (int) trim($refId);
                    $response = $moogold->orders()->details($refId);
                    $status = strtolower($response->getStatus());
                    $statuses[] = $status;
                } catch (\Exception $e) {
                    $log['errors'][] = [
                        'order_id' => $order->id,
                        'ref_id' => $refId,
                        'message' => $e->getMessage(),
                    ];
                    continue;
                }
            }

            $mappedStatuses = array_map(function ($status) {
                return match ($status) {
                    'refunded' => 'failed',
                    'pending', 'processing', 'completed', 'failed' => $status,
                    default => 'failed', // default fallback
                };
            }, $statuses);

            $uniqueStatuses = array_unique($mappedStatuses);
            $oldStatus = $order->status;
            $newStatus = null;

            if (count($uniqueStatuses) === 1) {
                $newStatus = $uniqueStatuses[0];
            } elseif (in_array('processing', $uniqueStatuses)) {
                $newStatus = 'processing';
            } elseif (in_array('failed', $uniqueStatuses)) {
                $newStatus = 'failed';
            } elseif (in_array('refunded', $uniqueStatuses)) {
                $newStatus = 'failed';
            }

            if ($newStatus && $newStatus !== $oldStatus) {
                $order->status = $newStatus;
                $order->save();

                $log['success_updates'][] = [
                    'order_id' => $order->id,
                    'reference_id' => $order->reference_id,
                    'from' => $oldStatus,
                    'to' => $newStatus,
                ];
            } else {
                $log['unchanged'][] = [
                    'order_id' => $order->id,
                    'reference_id' => $order->reference_id,
                    'status' => $oldStatus,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cronjob check completed',
            'log' => $log,
        ]);
    }

    // Tambahkan fungsi untuk cronjob pengecekan status Naelstore
    public function statusNaelstore()
    {
        $pendingOrders = Pembelian::whereIn('status', ['pending', 'processing'])
            ->whereHas('layanan.provider', function ($q) {
                $q->where('provider_name', 'naelstore');
            })->get();

        $log = [
            'success_updates' => [],
            'errors' => [],
            'unchanged' => [],
        ];

        $client = new Client();

        $naelstore = Provider::where('provider_name', 'naelstore')->first();
        $client = new Client([
            'base_uri' => $naelstore->base_url,
            'headers' => [
                'X-API-KEY' => $naelstore->api_key
            ]
        ]);

        foreach ($pendingOrders as $order) {
            try {

                $response = $client->post('order/status', [
                    'form_params' => [
                        'order_id' => $order->reference_id,
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                if (!isset($data['status']) || $data['status'] !== 'success') {
                    $log['errors'][] = [
                        'order_id' => $order->id,
                        'message' => $data['message'] ?? 'Invalid response from Naelstore'
                    ];
                    continue;
                }

                $newStatus = strtolower($data['data']['status']);
                $oldStatus = $order->status;

                if ($newStatus !== $oldStatus) {
                    $order->update(['status' => $newStatus]);

                    $payload = [
                        'data' => array_map('strval', [
                            'ref_id' => $order->reference_id,
                            'status' => $newStatus === 'completed' ? '1' : ($newStatus === 'failed' ? '2' : '0'),
                            'code' => $order->layanan->id,
                            'hp' => $order->input_zone ? $order->input_id . $order->input_zone : $order->input_id,
                            'price' => $order->total_price,
                            'message' => ucfirst($newStatus),
                            'balance' => $order->user->saldo ?? '',
                            'tr_id' => $order->order_id,
                            'rc' => $newStatus === 'completed' ? '00' : ($newStatus === 'failed' ? '07' : '39'),
                            'sn' => '',
                        ])
                    ];

                    // send 3x times with interval 3seconds
                    for ($i = 0; $i < 3; $i++) {
                        try {
                            $client->post(env('DIGIFLAZZ_H2H_CALLBACK'), $payload);
                            break;
                        } catch (\Exception $e) {
                            sleep(3);
                        }
                    }

                    $log['success_updates'][] = [
                        'order_id' => $order->order_id,
                        'from' => $oldStatus,
                        'to' => $newStatus,
                    ];
                } else {
                    $log['unchanged'][] = [
                        'order_id' => $order->order_id,
                        'status' => $oldStatus,
                    ];
                }
            } catch (\Exception $e) {
                $log['errors'][] = [
                    'order_id' => $order->order_id,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Naelstore status check completed',
            'log' => $log,
        ]);
    }
}
