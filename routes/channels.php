<?php

use Illuminate\Support\Facades\Broadcast;

// Pusher-compatible auth endpoint for the SPA: the Next.js client sends its
// Sanctum Bearer token, so this route authenticates via sanctum (not the
// web session). Without this line POST /broadcasting/auth 404s and every
// private-channel subscribe fails.
Broadcast::routes(['middleware' => ['auth:sanctum']]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private user channel for real-time messaging
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
