# Contributing to Atelier Noir

Thanks for taking the time to contribute! Bug reports, fixes, translations and documentation improvements are all welcome.

## Before you start

- Search [existing issues](https://github.com/wilfread237/atelier-noir/issues) first.
- For anything larger than a small fix, open an issue to discuss the idea before writing code.
- Security problems must **not** be reported in public issues — see [SECURITY.md](SECURITY.md).

## Repository layout

| Path | What it is |
| --- | --- |
| `/` | The child theme (parent: Hello Elementor) |
| `plugins/atelier-noir-core/` | Companion plugin: projects post type, SEO, newsletter, demo import, WooCommerce extras |
| `bin/build-release.sh` | Builds the release zips |
| `tests/` | PHPUnit unit tests |

## Local setup

1. Install WordPress locally (Local, wp-env, DDEV…) and the **Hello Elementor** parent theme.
2. Clone this repository into `wp-content/themes/atelier-noir`.
3. Symlink the companion plugin so edits are live:

   ```bash
   ln -s ../themes/atelier-noir/plugins/atelier-noir-core wp-content/plugins/atelier-noir-core
   ```

4. Activate the theme and the plugin, then run **Appearance → Atelier Noir Setup**.

## Coding standards

- Follow the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/) (tabs, Yoda conditions, escaping on output, sanitizing on input).
- Prefix everything: `atelier_noir_` for functions, options and hooks; `an_` for post types and short meta keys.
- Theme strings use the `atelier-noir` text domain; plugin strings use `atelier-noir-core`.
- Presentation belongs in the theme, features that must survive a theme switch belong in the plugin.
- Keep PHP 7.4 compatibility.

## Tests

```bash
composer install
composer test:unit
```

CI runs a PHP syntax check and the unit tests on every push and pull request.

## Pull requests

1. Fork the repository and create a branch from `main` (`fix/…`, `feat/…`, `docs/…`).
2. Keep pull requests focused — one topic per PR.
3. Describe what changed and how you tested it; add screenshots for visual changes.
4. Update `CHANGELOG.md` under **Unreleased**.

By contributing, you agree that your contributions are licensed under the GPL v3 or later, like the rest of the project.
