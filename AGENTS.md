# Agent guide: `banulakwin/laravel-eloquent-columns`

Portable traits and Blueprint macros for common columns. **Does not depend** on other `banulakwin/*` packages.

## Install

- Path or VCS repository in the host app; `composer require banulakwin/laravel-eloquent-columns`.
- Auto-discovery registers `EloquentColumnsServiceProvider`.

## Config

- Publish: `php artisan vendor:publish --tag=eloquent-columns-config` → `config/eloquent-columns.php`.
- `register_macros` (default `true`): set `false` if the app registers macros itself.

## Usage

- Migrations: `$table->activeColumn();`, `$table->metaColumns();`, etc.
- Models: `use Banulakwin\EloquentColumns\Concerns\HasActiveColumn;` (and siblings as needed).

## Testing & Quality

```bash
composer test          # PHPUnit (68 tests)
composer pint          # Laravel Pint code style fix
composer pint:check    # Pint check only (no fix)
composer phpstan       # PHPStan level max on src/
composer quality       # All: pint + phpstan + test
```

## CI

GitHub Actions runs tests, Pint, and PHPStan on push/PR (`.github/workflows/tests.yml`).

## Do not

- Add `require` entries for other `banulakwin/*` packages in this library.
- Commit generated files (`vendor/`, `composer.lock`, `coverage/`).
