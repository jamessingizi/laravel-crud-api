<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;

class SendUserDetails implements ShouldQueue
{
    public function __construct()
    {

    }

    public function handle(UserCreated $event): void
    {
//        make fake call to external service with newly created user's details
//        This will need to be replaced by a call to a real api
        Http::fake([
            'https://api.external-service.com/new-user' => Http::response([
                'data' => [
                    'name' => $event->user->name,
                    'email' => $event->user->email,
                    'phone_number' => $event->user->phone_number,
                    'role' => $event->user->role,
                ],
            ], 200)
        ]);
    }
}
