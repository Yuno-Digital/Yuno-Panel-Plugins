<?php

namespace Yuno\Plugins\HelloWorld;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Minimal example plugin: registers a web route at /plugin/hello.
 */
class HelloWorldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')->get('/plugin/hello', function () {
            return response()->json([
                'plugin' => 'hello-world',
                'message' => 'Hello from the Yuno plugin system!',
            ]);
        });
    }
}
