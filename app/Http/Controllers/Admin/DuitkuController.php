<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PaymentProvider;
use App\Http\Controllers\Controller;

class DuitkuController extends Controller
{
    private $duitkuConfig;
    public function __construct()
    {
        $provider = PaymentProvider::where('provider_name', 'Duitku')->first();

        $this->duitkuConfig = new \Duitku\Config($provider->api_key, $provider->kode_merchant);
        $this->duitkuConfig->setSandboxMode(true); // Set to false for production
    }

    public $apiKey, $merchantCode;

    public function create($merchantRef, $method, $price, $data, $return_url = null)
    {
        $user = User::find($data['id'] ?? null);

        $params = array(
            'paymentAmount'     => $price,
            'paymentMethod'     => $method,
            'merchantOrderId'   => $merchantRef,
            'productDetails'    => "Deposits",
            'customerVaName'    => $user->name ?? 'Guest',
            'email'             => $user->email ?? $data['email'],
            'callbackUrl'       => url('api/callback/duitku'),
            'returnUrl'         => $return_url,
        );


        try {
            // createInvoice Request
            $responseDuitkuApi = \Duitku\Api::createInvoice($params, $this->duitkuConfig);
            header('Content-Type: application/json');

            return $responseDuitkuApi;

            // $result = json_decode($responseDuitkuApi);


            // if ($result && $result->statusCode == 00) {
            //     return $result;
            // } else {
            //     throw new Exception($result->statusMessage);
            // }
        } catch (Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $e->getMessage()
                ]
            );
        }
    }
}
