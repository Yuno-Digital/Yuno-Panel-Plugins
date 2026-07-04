<?php

namespace Yuno\Plugins\DiscordBridge\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yuno\Plugins\DiscordBridge\Commands;
use Yuno\Plugins\DiscordBridge\Support\Discord;

class InteractionsController
{
    // Interaction types / response types from the Discord API.
    private const PING = 1;
    private const APPLICATION_COMMAND = 2;
    private const RESP_PONG = 1;
    private const RESP_MESSAGE = 4;
    private const RESP_DEFERRED = 5;
    private const EPHEMERAL = 64;

    public function handle(Request $request): JsonResponse
    {
        if (! Discord::verify($request)) {
            return response()->json(['error' => 'invalid signature'], 401);
        }

        $payload = (array) $request->json()->all();
        $type = $payload['type'] ?? null;

        if ($type === self::PING) {
            return response()->json(['type' => self::RESP_PONG]);
        }

        if ($type !== self::APPLICATION_COMMAND) {
            return response()->json(['type' => self::RESP_PONG]);
        }

        $userId = $payload['member']['user']['id'] ?? $payload['user']['id'] ?? null;
        if (! Discord::authorized($userId)) {
            return response()->json([
                'type' => self::RESP_MESSAGE,
                'data' => ['content' => '⛔ You are not allowed to control servers.', 'flags' => self::EPHEMERAL],
            ]);
        }

        $name = $payload['data']['name'] ?? '';
        $options = [];
        foreach ($payload['data']['options'] ?? [] as $option) {
            $options[$option['name']] = $option['value'] ?? null;
        }
        $token = (string) ($payload['token'] ?? '');

        // Answer within 3s with "thinking…", then edit it once the work is done.
        defer(fn () => Discord::followUp($token, Commands::run($name, $options)));

        return response()->json(['type' => self::RESP_DEFERRED]);
    }
}
