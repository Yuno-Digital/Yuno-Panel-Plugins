# Yuno Panel Plugins

Plugins for [Yuno Panel](../Yuno-Panel). Each plugin is a folder you drop into
the panel's `plugins/` directory; it then appears under **Admin → Plugins** and
can be enabled.

## Installing a plugin

```bash
cd /path/to/Yuno-Panel/plugins
git clone https://github.com/Yuno-Digital/Yuno-Panel-Plugins.git
# or copy a single plugin folder, e.g. hello-world/
```

Then enable it under **Admin → Plugins** and reload the panel.

## Plugin format

```
hello-world/
  plugin.json
  src/HelloWorldServiceProvider.php
```

`plugin.json`:

```json
{
  "id": "hello-world",
  "name": "Hello World",
  "version": "1.0.0",
  "description": "Example plugin that adds a /plugin/hello route.",
  "author": "Yuno Digital",
  "namespace": "Yuno\\Plugins\\HelloWorld",
  "provider": "Yuno\\Plugins\\HelloWorld\\HelloWorldServiceProvider"
}
```

When enabled, the panel registers `namespace` as a PSR-4 root at the plugin's
`src/` folder and boots `provider` — an ordinary Laravel service provider, so a
plugin can add routes, views, migrations, config, etc.

## Theming the panel

A plugin can restyle the panel by pushing CSS into the page `<head>` from its
provider's `boot()`. It's rendered after the app stylesheet, so it overrides the
defaults:

```php
use App\Support\Theme;

Theme::head('<style>:root{--yuno-accent-1:#f59e0b;--yuno-accent-2:#ef4444;--yuno-accent-3:#ec4899}</style>');
```

The `--yuno-accent-1/2/3` variables drive the brand wordmark and the aurora
background, so overriding them recolours the panel's signature look. For deeper
changes, inject a full `<style>` or `<link rel="stylesheet">`. See the
**accent-theme** plugin for a settings-driven example.

## Included plugins

- **hello-world** — registers `GET /plugin/hello` returning a JSON greeting; the
  smallest possible example.
- **discord-bridge** — control servers from Discord with slash commands
  (`/servers`, `/status`, `/power`, `/say`) via HTTP interactions. See
  [discord-bridge/README.md](discord-bridge/README.md).
- **accent-theme** — recolour the panel's accent (brand mark, aurora) from three
  hex colours in the plugin settings; demonstrates the theming hook.
