<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {

        if(! Auth::attempt($request->only(['email', 'password']))){
            return response()->json(['message' => 'invalid login'], 401);
        }

        $user = User::query()->where('email', $request->only('email'))->first();
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->api_token = $token;

        return response()->json($user, 200);
    }
}
