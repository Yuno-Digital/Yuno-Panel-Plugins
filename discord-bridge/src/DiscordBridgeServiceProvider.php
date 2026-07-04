<?php

namespace Yuno\Plugins\DiscordBridge;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Yuno\Plugins\DiscordBridge\Console\RegisterCommandsCommand;
use Yuno\Plugins\DiscordBridge\Http\InteractionsController;

class DiscordBridgeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Discord posts here for every slash command (no CSRF/session — the
        // request is verified by its Ed25519 signature instead).
        Route::post('/plugin/discord/interactions', [InteractionsController::class, 'handle']);

        if ($this->app->runningInConsole()) {
            $this->commands([RegisterCommandsCommand::class]);
        }
    }
}
