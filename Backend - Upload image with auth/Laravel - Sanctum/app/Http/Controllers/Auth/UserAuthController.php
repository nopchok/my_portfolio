<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserAuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($data)) {
            return response(['error' => 'Unauthorised'], 401);
        }

        $user = User::where('email', $data['email'])->first();
        $user->tokens()->delete();
        $token = $user->createToken('API Token')->plainTextToken;

        return response(['user' => $user, 'token' => $token]);
    }
    
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $data['password'] = bcrypt($request->password);

        $user = User::create($data);

        $token = $user->createToken('API Token')->plainTextToken;

        return response([ 'user' => $user, 'token' => $token]);
    }
}
