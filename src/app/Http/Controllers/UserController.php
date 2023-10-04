<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{

    public static function user(GetUserRequest $request): JsonResponse
    {
        if(auth()->user()->id != $request->id){
            return response()->json(['message' => 'unauthorised to view this resource'], 401);
        }
        $user = User::query()->findOrFail($request->id);
        return response()->json($user, 200);
    }


    public function updateUser(UpdateUserRequest $request): JsonResponse
    {
        if(auth()->user()->id != $request->id){
            return response()->json(['message' => 'unauthorised to view this resource'], 401);
        }

        $user = User::query()->findOrFail($request->id);
        $user->update(
            $request->only(['name', 'email', 'role', 'phone_number'])
        );
        return response()->json($user, 201);
    }


}
