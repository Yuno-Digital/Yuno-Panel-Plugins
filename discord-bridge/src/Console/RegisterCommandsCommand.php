<?php

namespace Yuno\Plugins\DiscordBridge\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RegisterCommandsCommand extends Command
{
    protected $signature = 'discord:register';

    protected $description = 'Register the Discord Bridge slash commands with Discord.';

    public function handle(): int
    {
        $appId = config('discord-bridge.app_id');
        $token = config('discord-bridge.bot_token');
        $guild = config('discord-bridge.guild_id');

        if (! $appId || ! $token) {
            $this->error('Set DISCORD_APP_ID and DISCORD_BOT_TOKEN first.');

            return self::FAILURE;
        }

        $stringOption = fn (string $name, string $desc, array $extra = []) => array_merge([
            'name' => $name, 'description' => $desc, 'type' => 3, 'required' => true,
        ], $extra);

        $commands = [
            ['name' => 'servers', 'description' => 'List all servers', 'type' => 1],
            ['name' => 'status', 'description' => 'Show a server\'s status', 'type' => 1, 'options' => [
                $stringOption('server', 'Server name'),
            ]],
            ['name' => 'power', 'description' => 'Start, stop or restart a server', 'type' => 1, 'options' => [
                $stringOption('server', 'Server name'),
                $stringOption('action', 'Power action', ['choices' => [
                    ['name' => 'start', 'value' => 'start'],
                    ['name' => 'stop', 'value' => 'stop'],
                    ['name' => 'restart', 'value' => 'restart'],
                ]]),
            ]],
            ['name' => 'say', 'description' => 'Send a console command to a server', 'type' => 1, 'options' => [
                $stringOption('server', 'Server name'),
                $stringOption('message', 'Command / message'),
            ]],
        ];

        $url = $guild
            ? "https://discord.com/api/v10/applications/{$appId}/guilds/{$guild}/commands"
            : "https://discord.com/api/v10/applications/{$appId}/commands";

        $response = Http::withHeaders(['Authorization' => 'Bot '.$token])->put($url, $commands);

        if ($response->successful()) {
            $this->info('Registered '.count($commands).' commands'.($guild ? " to guild {$guild}." : ' globally (may take up to an hour to appear).'));

            return self::SUCCESS;
        }

        $this->error('Discord API error '.$response->status().': '.$response->body());

        return self::FAILURE;
    }
}
