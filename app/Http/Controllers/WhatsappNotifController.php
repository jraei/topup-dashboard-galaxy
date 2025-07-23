<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Provider;
use Illuminate\Http\Request;

class WhatsappNotifController extends Controller
{
    private $api_key, $client;

    public function __construct()
    {
        $whatsappNotif = Provider::where('provider_name', 'whatsappNotif')->first();

        $this->api_key =  $whatsappNotif->api_key;

        $this->client = new Client([
            'base_uri' => $whatsappNotif->base_url,
        ]);
    }

    public function sendMessage(int $number, string $message)
    {
        $response = $this->client->request('POST', 'send-message', [
            'form_params' => [
                'id' => $number,
                'text' => $message,
                'type' => 'Text',
                'waKey' => $this->api_key
            ]
        ]);

        return $response->getBody()->getContents();
    }
}