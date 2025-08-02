<?php

namespace App\Http\Controllers;

use App\Models\FusionService;
use App\Models\Pembelian;
use App\Http\Controllers\MoogoldController;
use App\Http\Controllers\Admin\DigiflazzController;
use App\Http\Controllers\NaelstoreController;
use Illuminate\Support\Facades\Log;

class FusionOrderProcessor
{
    /**
     * Process fusion service order with individual API calls
     */
    public function processFusionOrder(FusionService $fusionService, array $orderData, string $orderId)
    {
        $services = $fusionService->services();
        $successfulTransactions = [];
        $failedTransactions = [];
        $totalProcessed = 0;
        $totalSuccessful = 0;

        foreach ($services as $service) {
            $serviceOrderId = $orderId . '-S' . $service->id;
            $provider = strtolower($service->produk->provider->provider_name);

            try {
                $result = $this->processServiceOrder($service, $orderData, $serviceOrderId, $provider);

                if ($result['success']) {
                    $successfulTransactions[] = [
                        'service_id' => $service->id,
                        'service_name' => $service->nama_layanan,
                        'provider' => $provider,
                        'order_id' => $serviceOrderId,
                        'reference_id' => $result['reference_id'] ?? null,
                        'status' => $result['status'] ?? 'processing',
                        'processed_at' => now()->toISOString(),
                    ];
                    $totalSuccessful++;
                } else {
                    $failedTransactions[] = [
                        'service_id' => $service->id,
                        'service_name' => $service->nama_layanan,
                        'provider' => $provider,
                        'order_id' => $serviceOrderId,
                        'error' => $result['error'] ?? 'Unknown error',
                        'failed_at' => now()->toISOString(),
                    ];
                }

                $totalProcessed++;

                // Small delay between API calls to prevent rate limiting
                usleep(100000); // 100ms delay

            } catch (\Exception $e) {
                Log::error("Fusion service processing error for service {$service->id}: " . $e->getMessage());

                $failedTransactions[] = [
                    'service_id' => $service->id,
                    'service_name' => $service->nama_layanan,
                    'provider' => $provider,
                    'order_id' => $serviceOrderId,
                    'error' => $e->getMessage(),
                    'failed_at' => now()->toISOString(),
                ];
                $totalProcessed++;
            }
        }

        return [
            'total_services' => $services->count(),
            'total_processed' => $totalProcessed,
            'total_successful' => $totalSuccessful,
            'total_failed' => count($failedTransactions),
            'successful_transactions' => $successfulTransactions,
            'failed_transactions' => $failedTransactions,
            'overall_status' => $totalSuccessful > 0 ? 'partial_success' : 'failed',
        ];
    }

    /**
     * Process individual service order through provider API
     */
    private function processServiceOrder($service, array $orderData, string $serviceOrderId, string $provider)
    {
        $inputId = $orderData['input_id'];
        $inputZone = $orderData['input_zone'];
        $quantity = $orderData['quantity'];
        $target = $inputId . ($inputZone ? $inputZone : '');

        switch ($provider) {
            case 'moogold':
                return $this->processMoogoldService($service, $orderData, $serviceOrderId);

            case 'digiflazz':
                return $this->processDigiflazzService($service, $target, $serviceOrderId, $quantity);

            case 'naelstore':
                return $this->processNaelstoreService($service, $target, $serviceOrderId, $quantity);

            case 'manual':
                return [
                    'success' => true,
                    'status' => 'processing',
                    'reference_id' => $serviceOrderId,
                    'message' => 'Manual service - will be processed by admin',
                ];

            default:
                return [
                    'success' => false,
                    'error' => "Unsupported provider: {$provider}",
                ];
        }
    }

    private function processMoogoldService($service, array $orderData, string $serviceOrderId)
    {
        try {
            $moogold = new MoogoldController();
            $result = $moogold->createTransaction([
                'category_id' => $service->produk->moogold_order_category,
                'order_id' => $serviceOrderId,
                'service_id' => $service->kode_layanan,
                'quantity' => $orderData['quantity'],
                'user_id' => $orderData['input_id'],
                'server' => $orderData['input_zone']
            ]);

            if (!$result['data']['status']) {
                return [
                    'success' => false,
                    'error' => $result['data']['raw']['err_message'] ?? $result['data']['message']
                ];
            }

            $data = $result['data'];
            $status = ($data['response']['status'] == "pending" || $data['response']['status'] == "processing") ? "processing" : "failed";

            return [
                'success' => $status === 'processing',
                'status' => $status,
                'reference_id' => $data['order_id'],
                'response_data' => $data,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function processDigiflazzService($service, string $target, string $serviceOrderId, int $quantity)
    {
        try {
            $successfulOrders = [];
            $failedOrders = [];

            // Digiflazz needs individual calls for each quantity
            for ($i = 0; $i < $quantity; $i++) {
                $subOrderId = $serviceOrderId . '-' . ($i + 1);

                $digiflazz = new DigiflazzController();
                $result = $digiflazz->createTransaction([
                    'kode_layanan' => $service->kode_layanan,
                    'target' => $target,
                    'ref_id' => $subOrderId,
                    'testing' => true,
                ]);

                if (!isset($result['status']) || !in_array($result['status'], ["Pending", "Sukses"])) {
                    $failedOrders[] = [
                        'sub_order_id' => $subOrderId,
                        'error' => $result['message'] ?? "Digiflazz API error"
                    ];
                } else {
                    $successfulOrders[] = [
                        'sub_order_id' => $subOrderId,
                        'reference_id' => $result['ref_id'],
                        'status' => $result['status']
                    ];
                }
            }

            return [
                'success' => count($successfulOrders) > 0,
                'status' => count($successfulOrders) > 0 ? 'processing' : 'failed',
                'reference_id' => implode(',', array_column($successfulOrders, 'reference_id')),
                'successful_orders' => $successfulOrders,
                'failed_orders' => $failedOrders,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function processNaelstoreService($service, string $target, string $serviceOrderId, int $quantity)
    {
        try {
            $naelstore = new NaelstoreController();
            $result = $naelstore->createTransaction([
                'layanan_id' => $service->kode_layanan,
                'quantity' => $quantity,
                'target' => $target,
            ]);

            if (!$result['status']) {
                return [
                    'success' => false,
                    'error' => $result['message'] ?? "Naelstore API error"
                ];
            }

            $data = $result['data'];
            $status = $data['status'] == true ? "processing" : "failed";

            return [
                'success' => $status === 'processing',
                'status' => $status,
                'reference_id' => $data['order_id'],
                'response_data' => $data,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
