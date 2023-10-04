<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\GetUserRequest;
use App\Http\Requests\GetUsersRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminController extends Controller
{
    public static function users(GetUsersRequest $request): AnonymousResourceCollection
    {
        $perPage = $request->query('per_page', 10);
        $users = User::query()->latest()->paginate($perPage);

        return UserResource::collection($users);
    }

    public static function user(GetUserRequest $request): JsonResponse
    {
        $user = User::query()->findOrFail($request->id);

        return response()->json($user, 200);
    }

    public function createUser(CreateUserRequest $request): JsonResponse
    {
        $user = User::create($request->all());
        if (! $user) {
            return response()->json(['message' => 'error creating user'], 400);
        }
        event(new UserCreated($user));

        return response()->json($user, 201);
    }

    public function updateUser(UpdateUserRequest $request): JsonResponse
    {
        $user = User::query()->findOrFail($request->id);
        $user->update(
            $request->only(['name', 'email', 'role', 'phone_number'])
        );

        return response()->json($user, 201);
    }
}
