<?php

namespace Yuno\Plugins\DiscordBridge\Support;

use App\Support\PluginManager;

/**
 * Reads the plugin's config from the panel (Admin → Plugins → Settings), with
 * an env fallback (e.g. DISCORD_PUBLIC_KEY) so both approaches work.
 */
class Config
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = PluginManager::setting('discord-bridge', $key);
        if ($value !== null && $value !== '') {
            return $value;
        }

        $env = env('DISCORD_'.strtoupper($key));

        return $env !== null && $env !== '' ? $env : $default;
    }

    /**
     * The allow-listed Discord user ids.
     *
     * @return array<int, string>
     */
    public static function adminIds(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) self::get('admin_ids')))));
    }
}
