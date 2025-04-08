
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function index()
    {
        $banners = Banner::where('status', 'active')
                        ->orderBy('order')
                        ->get(['id', 'image_path']);

        return Inertia::render('User/Index', [
            'banners' => $banners
        ]);
    }
}
