# Discord Bot Bridge

Control your servers from Discord with slash commands, using Discord's **HTTP
interactions** (no always-on bot process needed — Discord POSTs to the panel).

## Commands

| Command | Description |
|---------|-------------|
| `/servers` | List all servers |
| `/status server:<name>` | Show a server's live state |
| `/power server:<name> action:<start\|stop\|restart>` | Power action |
| `/say server:<name> message:<text>` | Send a console command |

Server is matched by name (partial) or UUID. Only allow-listed Discord users may
run the commands.

## Setup

1. Create an application at <https://discord.com/developers/applications>, add a
   bot, and copy the **Public Key**, **Application ID** and **Bot Token**.

2. Add to the panel's `.env`:

   ```dotenv
   DISCORD_PUBLIC_KEY=...
   DISCORD_APP_ID=...
   DISCORD_BOT_TOKEN=...
   DISCORD_ADMIN_IDS=<your discord user id>,<another>
   # optional, for instant command updates while testing:
   DISCORD_GUILD_ID=<server id>
   ```

3. Enable the plugin under **Admin → Plugins**, then register the commands:

   ```bash
   php artisan discord:register
   ```

4. In the Developer Portal, set the **Interactions Endpoint URL** to:

   ```
   https://your-panel.example.com/plugin/discord/interactions
   ```

   Discord will send a test ping; the plugin verifies it and it will save.

## How it works

- `POST /plugin/discord/interactions` verifies each request's Ed25519 signature
  against your public key, then answers with a deferred response and edits it
  with the result — so slow node calls never hit Discord's 3-second limit.
- Actions run through the panel's own `Server` model and `WingsClient`.

> Security: anyone who is **not** in `DISCORD_ADMIN_IDS` is refused. Keep the bot
> token and public key secret.
