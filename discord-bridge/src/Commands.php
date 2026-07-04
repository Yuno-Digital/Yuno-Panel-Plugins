<?php

namespace Yuno\Plugins\DiscordBridge;

use App\Models\Server;
use App\Services\WingsClient;

/**
 * Runs a Discord slash command against the panel and returns a text reply.
 */
class Commands
{
    /**
     * @param  array<string, mixed>  $options
     */
    public static function run(string $name, array $options): string
    {
        return match ($name) {
            'servers' => self::servers(),
            'status' => self::status((string) ($options['server'] ?? '')),
            'power' => self::power((string) ($options['server'] ?? ''), (string) ($options['action'] ?? '')),
            'say' => self::say((string) ($options['server'] ?? ''), (string) ($options['message'] ?? '')),
            default => 'Unknown command.',
        };
    }

    private static function find(string $query): ?Server
    {
        return Server::query()
            ->where('name', 'like', '%'.$query.'%')
            ->orWhere('uuid', $query)
            ->first();
    }

    private static function servers(): string
    {
        $servers = Server::orderBy('name')->get();

        if ($servers->isEmpty()) {
            return 'No servers.';
        }

        return "**Servers**\n".$servers->map(fn (Server $s) => '• '.$s->name)->implode("\n");
    }

    private static function status(string $query): string
    {
        $server = self::find($query);
        if (! $server) {
            return "Server not found: `{$query}`";
        }

        $stats = app(WingsClient::class)->stats($server->load('node'));
        $state = $stats['state'] ?? 'unreachable';

        return "**{$server->name}** — `{$state}`";
    }

    private static function power(string $query, string $action): string
    {
        if (! in_array($action, ['start', 'stop', 'restart'], true)) {
            return 'Action must be start, stop or restart.';
        }

        $server = self::find($query);
        if (! $server) {
            return "Server not found: `{$query}`";
        }

        $ok = app(WingsClient::class)->power($server->load('node'), $action);

        return $ok
            ? "✅ `{$action}` sent to **{$server->name}**."
            : "⚠️ Could not reach the node for **{$server->name}**.";
    }

    private static function say(string $query, string $message): string
    {
        $server = self::find($query);
        if (! $server) {
            return "Server not found: `{$query}`";
        }

        $ok = app(WingsClient::class)->command($server->load('node'), $message);

        return $ok
            ? "✅ Sent to **{$server->name}**: `{$message}`"
            : "⚠️ Could not send the command to **{$server->name}**.";
    }
}
