
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AjaxController extends Controller
{
    /**
     * Make a GET request to an external API
     */
    public function getAjax($url)
    {
        // For security, restrict URLs to only trusted domains
        $allowedDomains = [
            'api.example.com',
            'api.digiflazz.com',
            'api.tripay.co.id'
        ];
        
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';
        
        if (!in_array($host, $allowedDomains)) {
            return response()->json([
                'error' => 'Unauthorized domain',
                'message' => 'The requested domain is not in the allowed list'
            ], 403);
        }
        
        // Cache results for 5 minutes to reduce API calls
        $cacheKey = 'ajax_get_' . md5($url);
        
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }
        
        try {
            $response = Http::get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                Cache::put($cacheKey, $data, 300); // Cache for 5 minutes
                return response()->json($data);
            }
            
            return response()->json([
                'error' => 'External API error',
                'message' => 'The external API returned an error',
                'status' => $response->status()
            ], $response->status());
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Request failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Make a POST request to an external API
     */
    public function postAjax(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'url' => 'required|url',
            'data' => 'required|array'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation error',
                'message' => $validator->errors()
            ], 422);
        }
        
        $url = $request->url;
        $data = $request->data;
        
        // For security, restrict URLs to only trusted domains
        $allowedDomains = [
            'api.example.com',
            'api.digiflazz.com',
            'api.tripay.co.id'
        ];
        
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';
        
        if (!in_array($host, $allowedDomains)) {
            return response()->json([
                'error' => 'Unauthorized domain',
                'message' => 'The requested domain is not in the allowed list'
            ], 403);
        }
        
        try {
            $response = Http::post($url, $data);
            
            return response()->json([
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Request failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get latest data for dashboard
     */
    public function getDashboardData()
    {
        // This would typically fetch real data from multiple sources
        // For demonstration, we'll return mock data
        
        $data = [
            'stats' => [
                'users' => [
                    'total' => 1250,
                    'growthPercent' => 12.5,
                    'isPositive' => true
                ],
                'revenue' => [
                    'total' => 18500000,
                    'currency' => "IDR",
                    'growthPercent' => 8.2,
                    'isPositive' => true
                ],
                'orders' => [
                    'total' => 3784,
                    'growthPercent' => -2.4,
                    'isPositive' => false
                ],
                'products' => [
                    'total' => 152,
                    'growthPercent' => 5.1,
                    'isPositive' => true
                ]
            ],
            'recentTransactions' => [
                ['id' => 'TX-1234', 'user' => 'John Doe', 'amount' => 250000, 'status' => 'completed', 'date' => '2023-09-15', 'game' => 'Mobile Legends'],
                ['id' => 'TX-1235', 'user' => 'Jane Smith', 'amount' => 499000, 'status' => 'pending', 'date' => '2023-09-15', 'game' => 'Free Fire'],
                ['id' => 'TX-1236', 'user' => 'Robert Brown', 'amount' => 150000, 'status' => 'completed', 'date' => '2023-09-14', 'game' => 'PUBG Mobile'],
                ['id' => 'TX-1237', 'user' => 'Alice Johnson', 'amount' => 1000000, 'status' => 'failed', 'date' => '2023-09-14', 'game' => 'Genshin Impact'],
                ['id' => 'TX-1238', 'user' => 'Chris Wilson', 'amount' => 305000, 'status' => 'completed', 'date' => '2023-09-13', 'game' => 'Mobile Legends'],
            ],
            'topProducts' => [
                ['id' => 1, 'name' => 'Mobile Legends', 'sales' => 1280, 'revenue' => 15360000, 'growth' => 12.4],
                ['id' => 2, 'name' => 'Free Fire', 'sales' => 980, 'revenue' => 11760000, 'growth' => 8.1],
                ['id' => 3, 'name' => 'PUBG Mobile', 'sales' => 820, 'revenue' => 9840000, 'growth' => 5.7],
                ['id' => 4, 'name' => 'Genshin Impact', 'sales' => 650, 'revenue' => 7800000, 'growth' => 10.2],
                ['id' => 5, 'name' => 'Valorant', 'sales' => 520, 'revenue' => 6240000, 'growth' => -2.3],
            ]
        ];
        
        return response()->json($data);
    }
}
