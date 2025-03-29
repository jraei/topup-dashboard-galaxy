
<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard');
    }
    
    public function settings()
    {
        return Inertia::render('Admin/Settings');
    }
    
    public function categories()
    {
        return Inertia::render('Admin/Categories');
    }
    
    public function banners()
    {
        return Inertia::render('Admin/Banners');
    }
}
