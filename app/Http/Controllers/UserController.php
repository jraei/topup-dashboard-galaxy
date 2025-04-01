<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::oldest();
        if (request('keyword')) {
            $users->where('username', 'like', '%' . request('keyword') . '%')
                ->orWhere('email', 'like', '%'  . request('keyword') . '%')
                ->orWhere('phone', 'like', '%' . request('keyword') . '%');
        }
        return view('admin.user.index', ['users' => $users->paginate(5)->withQueryString()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|max:25|alpha_num|unique:users,username',
            'email' => 'required|email:dns|unique:users,email',
            'phone' => 'required|numeric',
            'saldo' => 'required|numeric',
            'level' => 'required',
            'password' => 'required|confirmed|min:6',
            'status' => 'required'
        ]);
        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        return back()->with('success', 'New User has been added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json([
            "message" => "Berhasil Show Data!",
            "type" => request('type'),
            "data" => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return response()->json([
            "message" => "Berhasil Show Data Edit!",
            "data" => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'username' => 'required|max:25|alpha_num|unique:users,username',
            'email' => 'required|email:dns|unique:users,email',
            'phone' => 'required|numeric',
            'saldo' => 'required|numeric',
            'level' => 'required',
            'password' => 'required|min:6',
            'status' => 'required'
        ]);
        $validatedData['password'] = Hash::make($validatedData['password']);

        User::where('id', $user->id);

        return back()->with('success', 'New User has been added!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        User::destroy($user->id);
        return back()->with('success', 'User has been deleted!');
    }
}
