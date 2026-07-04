<?php

return [
    // From the Discord Developer Portal → your application.
    'public_key' => env('DISCORD_PUBLIC_KEY'),
    'app_id' => env('DISCORD_APP_ID'),
    'bot_token' => env('DISCORD_BOT_TOKEN'),

    // Discord user IDs allowed to run the commands (comma-separated).
    'admin_ids' => array_values(array_filter(array_map('trim', explode(',', (string) env('DISCORD_ADMIN_IDS'))))),

    // Optional: register commands to a single guild (faster to update while testing).
    'guild_id' => env('DISCORD_GUILD_ID'),
];
