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

## Do not

- Add `require` entries for other `banulakwin/*` packages in this library.
