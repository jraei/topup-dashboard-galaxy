<?php

namespace App\Http\Controllers\Admin;

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
        $productsResult = $moogoldController->getMoogoldProducts();

        // Sinkronisasi layanan
        $servicesResult = $moogoldController->getMoogoldServices();

        // Format hasil
        $result = [
            'status' => true,
            'timestamp' => now()->toDateTimeString(),
            'products' => [
                'success' => $productsResult['success'] ?? 0,
                'failed' => $productsResult['failed'] ?? 0,
                'message' => isset($productsResult['message']) ? $productsResult['message'] : 'Product sync completed'
            ],
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
        $pendingOrders = Pembelian::where('status', 'pending')->whereHas('layanan.provider', function ($q) {
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
                    $response = $moogold->orders()->details($refId);
                    $status = strtolower($response['order_status']);
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

            $uniqueStatuses = array_unique($statuses);
            $oldStatus = $order->status;
            $newStatus = null;

            if (count($uniqueStatuses) === 1) {
                $newStatus = $uniqueStatuses[0];
            } elseif (in_array('processing', $uniqueStatuses)) {
                $newStatus = 'processing';
            } elseif (in_array('failed', $uniqueStatuses)) {
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
}