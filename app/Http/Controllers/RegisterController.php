<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // Halaman register
    public function showRegisterForm()
    {
        return view('auth.register'); // layout guest
    }

    // Proses register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $role = $request->username === 'admin' ? 'admin' : 'user';

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        Auth::login($user);

        return $role === 'admin'
            ? redirect('/dashboard/admin')
            : redirect('/dashboard/user');
    }
}
