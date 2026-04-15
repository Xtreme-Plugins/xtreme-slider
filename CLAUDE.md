# XtremeSlider — Agent Guidelines

A free, lightweight WordPress slider plugin with three layout modes (Default, Cool, 3D). Frontend uses pure vanilla JavaScript with zero external dependencies. Submitted to WordPress.org plugin directory.

## Project Structure

```
xtreme-slider.php                  — Main plugin entry point, defines constants, hooks
uninstall.php                      — Clean uninstall: drops tables and options
readme.txt                         — WordPress.org readme (used by the plugin directory)
CHANGELOG.md                       — Full changelog
assets/
  css/admin.css                    — Admin panel styles
  css/slider.css                   — Frontend slider styles
  js/admin.js                      — Admin panel JavaScript
  js/slider.js                     — Frontend slider JavaScript (vanilla JS, no jQuery)
  img/                             — Plugin images and icons
inc/
  functions.php                    — Global helper functions (xtrsl_ prefixed)
  class-activator.php              — Activation: creates DB tables, sets defaults
  class-deactivator.php            — Deactivation: flushes rewrite rules
  class-plugin.php                 — Main plugin class, bootstraps admin + frontend
  admin/
    class-admin.php                — Admin menus, asset enqueuing, form routing
    class-admin-sliders.php        — Slider list page
    class-admin-edit.php           — Slider edit/create page
    class-admin-settings.php       — Global settings page
  front/
    class-shortcode.php            — Registers [xtreme_slider] shortcode
    class-renderer.php             — Outputs slider HTML
```

## Branch Strategy

| Branch      | Purpose |
|-------------|---------|
| `main`      | Active development, latest features |
| `wordpress` | WordPress.org submission-ready code — keep in sync with main after each release |

When preparing a WordPress.org submission:
1. Make all changes on `main`
2. Merge `main` into `wordpress`
3. Zip and submit `wordpress` branch content to WordPress.org

## WordPress.org Compliance — CRITICAL

### Prefix Rules

**ALL** PHP-level identifiers must use the `xtrsl_` prefix (minimum 4 characters, unique to this plugin).

| Type | Correct prefix | Example |
|------|---------------|---------|
| PHP constants (`define`) | `XTRSL_` | `XTRSL_VERSION`, `XTRSL_PLUGIN_PATH` |
| PHP classes | `Xtrsl_` | `Xtrsl_Admin`, `Xtrsl_Plugin` |
| PHP functions | `xtrsl_` | `xtrsl_get_slider()`, `xtrsl_setting()` |
| WordPress options | `xtrsl_` | `update_option('xtrsl_settings', ...)` |
| Database tables | `xtrsl_` | `{$wpdb->prefix}xtrsl_sliders` |
| Admin menu slugs | `xtrsl-` | `xtrsl-edit`, `xtrsl-settings` |
| Script/style handles | `xtrsl-` | `xtrsl-admin`, `xtrsl-slider` |
| JS localized objects | `xtrsl` | `xtrslAdmin` |
| Nonce actions/names | `xtrsl_` | `xtrsl_save_slider`, `xtrsl_edit_nonce` |

**Do NOT use `xs_`, `XS_`, or `xs-`** — these are the old 2-character prefixes that caused the WordPress.org rejection.

### readme.txt Requirements

- `Contributors:` must include the WordPress.org username of the plugin owner (`loanpartnership`). Current value: `xtremeplugins, loanpartnership`
- `Donate link:` must be a publicly accessible URL with no redirects. If the URL returns a 3xx redirect, remove the line entirely.
- `Stable tag:` must match the version in the plugin header and `XTRSL_VERSION` constant.

### Other Requirements

- No `if (!function_exists(...))` wrappers around plugin classes or functions — use proper prefixes instead
- All user-facing strings must use the `xtreme-slider` text domain
- Assets must only load on pages that use a slider (conditional enqueuing — already implemented)

## Database Tables

| Table | Purpose |
|-------|---------|
| `{prefix}xtrsl_sliders` | Slider configurations |
| `{prefix}xtrsl_slides` | Individual slides per slider |

DB version is tracked in the `xtrsl_db_version` option. Schema migrations run via `Xtrsl_Activator::maybe_upgrade()` on every page load (cheap version check).

## Constants

| Constant | Value |
|----------|-------|
| `XTRSL_VERSION` | Current plugin version (e.g. `1.2.1`) |
| `XTRSL_DB_VERSION` | DB schema version integer |
| `XTRSL_PLUGIN_FILE` | Absolute path to main plugin file |
| `XTRSL_PLUGIN_PATH` | Absolute path to plugin directory (trailing slash) |
| `XTRSL_PLUGIN_URL` | URL to plugin directory (trailing slash) |
| `XTRSL_PLUGIN_BASENAME` | Plugin basename for WP APIs |

## Shortcode

`[xtreme_slider id="X"]`

Optional overrides: `layout`, `visible`, `autoplay`, `fullscreen`

Example: `[xtreme_slider id="5" layout="3d" visible="2" autoplay="true" fullscreen="true"]`

## Versioning

Follow semver:
- **Patch** (x.x.1) — bug fixes, compliance changes, no new features
- **Minor** (x.1.0) — new features, backwards compatible
- **Major** (1.0.0) — breaking changes

When bumping the version, update ALL three locations:
1. Plugin header in `xtreme-slider.php` (`Version:`)
2. `XTRSL_VERSION` constant in `xtreme-slider.php`
3. `Stable tag:` in `readme.txt`
4. Add entry to `== Changelog ==` in `readme.txt`
5. Add entry to `CHANGELOG.md`

## Conventions

- WordPress Coding Standards (tabs, Yoda conditions, `esc_*` on all output)
- `wp_safe_redirect()` + `exit` for all form POST redirects
- Nonces on every form and every destructive GET action
- `current_user_can('manage_options')` checked before any write operation
- Direct DB queries (`$wpdb`) are used for custom tables — add `phpcs:ignore` comments with justification
- CSS class names in HTML templates use `xs-` prefix — this is fine (HTML/CSS classes are not PHP identifiers and don't need renaming)

## Common Pitfalls

- **Do NOT revert to `xs_` prefixes** — WordPress.org will reject the submission
- **Do NOT add `wp_` or `_` (single underscore) prefixes** — reserved for WordPress core
- **Do NOT use `if (!function_exists(...))` guards** — use proper `xtrsl_` prefixes instead
- **Do NOT edit the zip file** — regenerate it from source: `cd wp-content/plugins && zip -r xtreme-slider/xtreme-slider.zip xtreme-slider --exclude "xtreme-slider/xtreme-slider.zip"`
- **Do NOT commit the zip file** to the `wordpress` branch on GitHub
- The shortcode tag `xtreme_slider` stays as-is — it's the public API and cannot change without breaking existing sites
