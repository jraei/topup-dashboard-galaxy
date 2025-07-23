<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\UserRole;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\WhatsappNotifController;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        // Apply rate limiting for registration page visits
        $key = 'registration_view:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            abort(429, 'Too many registration attempts. Please try again later.');
        }

        RateLimiter::hit($key, 60); // 1 minute

        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:25|alpha_num|unique:' . User::class,
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'username.unique' => 'Username sudah terdaftar, silakan gunakan yang lain.',
            'email.unique' => 'Email sudah digunakan. Silakan coba yang lain.',
            'phone.unique' => 'Nomor telepon sudah digunakan. Silakan coba yang lain.'
        ]);

        $roleBasic = UserRole::where('role_name', 'basic')->first();

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_role_id' => $roleBasic->id, // Assuming 3 is for regular users
        ]);

        event(new Registered($user));

        Auth::login($user);

        $waNotif = new WhatsappNotifController();
        $judulWeb = WebConfig::where('key', 'judul_web')->first()->value ?? env('APP_NAME');
        // Kirim notifikasi WhatsApp
        $res = $waNotif->sendMessage($request->phone, 'Halo ' . $request->name . ', Terima kasih telah melakukan pendaftaran di ' . $judulWeb . '. Akun kamu sudah dapat kamu gunakan untuk transaksi dan menikmati semua fitur yang kami tawarkan!');

        $result = json_decode($res);
        if ($result->statusCode == 200) {
            logger()->info("Success to send whatsapp notif [REGISTER ACCOUNT]: " . $result->message);
        } else {
            logger()->error("Failed to send whatsapp notif [REGISTER ACCOUNT]: " . $result->message);
        }

        return redirect(RouteServiceProvider::HOME);
    }
}