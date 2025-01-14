<?php

namespace App\Http\Controllers;

use App\Models\PaymentProvider;
use Illuminate\Http\Request;

class PaymentProviderController extends Controller
{
    protected function getProvider($name)
    {
        return PaymentProvider::where('provider_name', $name)->get();
    }
}
