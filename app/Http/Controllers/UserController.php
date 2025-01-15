<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $data = [
            "id" => $user->id,
            "username" => $user->username,
            "phone" => $user->phone,
            "email" => $user->email,
            "saldo" => $user->saldo,
            "level" => $user->level,
            "status" => $user->status
        ];
        return view('admin.user.index', ['user_detail' => $data])->with('success', 'Sukses show data');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
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
