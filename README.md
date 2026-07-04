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

## Included plugins

- **hello-world** — registers `GET /plugin/hello` returning a JSON greeting; the
  smallest possible example.
