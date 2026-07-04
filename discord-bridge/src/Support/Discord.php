<?php

namespace Yuno\Plugins\DiscordBridge\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class Discord
{
    /**
     * Verify a Discord interaction request's Ed25519 signature against the raw
     * body. Discord requires this before it will trust the endpoint.
     */
    public static function verify(Request $request): bool
    {
        $signature = $request->header('X-Signature-Ed25519');
        $timestamp = $request->header('X-Signature-Timestamp');
        $publicKey = (string) config('discord-bridge.public_key');

        if (! $signature || ! $timestamp || ! $publicKey) {
            return false;
        }

        try {
            return sodium_crypto_sign_verify_detached(
                sodium_hex2bin($signature),
                $timestamp.$request->getContent(),
                sodium_hex2bin($publicKey),
            );
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Whether a Discord user id is allowed to run commands.
     */
    public static function authorized(?string $userId): bool
    {
        $allowed = (array) config('discord-bridge.admin_ids', []);

        return $userId !== null && in_array($userId, $allowed, true);
    }

    /**
     * Edit the deferred ("thinking…") response with the final content.
     */
    public static function followUp(string $interactionToken, string $content): void
    {
        $appId = config('discord-bridge.app_id');

        Http::patch(
            "https://discord.com/api/v10/webhooks/{$appId}/{$interactionToken}/messages/@original",
            ['content' => $content],
        );
    }
}
