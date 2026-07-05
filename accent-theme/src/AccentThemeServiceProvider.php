<?php

namespace Yuno\Plugins\AccentTheme;

use App\Support\PluginManager;
use App\Support\Theme;
use Illuminate\Support\ServiceProvider;

/**
 * Recolours the panel's accent by overriding the --yuno-accent-* CSS variables
 * with the colours configured in Admin → Plugins → Accent Theme → Settings.
 */
class AccentThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Theme::head(function (): string {
            $vars = [
                '--yuno-accent-1' => self::color('accent_1', '#6366f1'),
                '--yuno-accent-2' => self::color('accent_2', '#a855f7'),
                '--yuno-accent-3' => self::color('accent_3', '#ec4899'),
            ];

            $decls = '';
            foreach ($vars as $name => $value) {
                $decls .= "{$name}:{$value};";
            }

            return "<style>:root{{$decls}}</style>";
        });
    }

    /**
     * A stored colour if it looks like a hex value, otherwise the default.
     */
    private static function color(string $key, string $default): string
    {
        $value = trim((string) PluginManager::setting('accent-theme', $key));

        return preg_match('/^#[0-9a-fA-F]{3,8}$/', $value) === 1 ? $value : $default;
    }
}
