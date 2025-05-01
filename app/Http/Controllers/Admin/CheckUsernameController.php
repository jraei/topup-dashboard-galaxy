<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use GuzzleHttp\Client;

class CheckUsernameController extends Controller
{

    public function getAccountUsername(array $data)
    {
        $provider = Provider::where('provider_name', 'checkUsername')->first();
        $api_key = $provider->api_key;
        $base_url =  $provider->base_url;

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $base_url,
        ]);

        // Try catch
        try {
            $params = [
                'api_key' => $api_key,
                'game' => $data['game'],
                'user_id' => $data['user_id'],
            ];
            if (isset($data['zone_id'])) {
                $params['zone_id'] = $data['zone_id'];
            }

            $response = $client->request('POST', [
                'form_params' => $params
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            // check if status success
            if ($data['status'] != 'success') {
                return response()->json(['status' => $data['status'], 'message' => $data['message'] ?? ''], 500);
            }
            $username = $data['result']['username'];

            return response()->json(['username' => $username]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
