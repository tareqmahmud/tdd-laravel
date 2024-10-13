<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $hash_password = Hash::make($request->password);
        $newUser = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $hash_password
        ]);

        return response()->json($newUser, Response::HTTP_CREATED);
    }
}
