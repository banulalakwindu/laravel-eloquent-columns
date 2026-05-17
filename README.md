# Laravel Eloquent Columns (`banulakwin/laravel-eloquent-columns`)

Portable **Eloquent traits** and **Blueprint macros** for common column patterns: **active**, **featured**, **sort order**, **timestamps + soft deletes**, **slug pair** (via **Spatie Sluggable**), and **audit user columns** (`created_by`, `updated_by`, `deleted_by`).

Activity/event logging is **not** included (intended as a separate package later).

---

## Requirements

- PHP `^8.4`
- Laravel `illuminate/*` `^11.0|^12.0|^13.0`
- `spatie/laravel-sluggable` `^3.8` (for `HasSlugColumns`)

---

## Installation

Auto-discovery registers `Banulakwin\EloquentColumns\EloquentColumnsServiceProvider`.

```bash
composer require banulakwin/laravel-eloquent-columns
```

Optional publish:

```bash
php artisan vendor:publish --tag=eloquent-columns-config
```

| Config key | Purpose |
|------------|---------|
| `register_macros` | Register Blueprint macros on boot (default `true`). Env: `ELOQUENT_COLUMNS_REGISTER_MACROS`. |
| `user_model` | Class for `HasAuditColumns` `creator()` / `updater()` / `deleter()`. Falls back to `auth.providers.users.model` if empty. Env: `ELOQUENT_COLUMNS_USER_MODEL`. |

---

## Traits (`Banulakwin\EloquentColumns\Concerns`)

| Trait | Notes |
|-------|--------|
| `HasActiveColumn` | `is_active` scope `active()`, helpers `markActive` / `markInactive`. Override `getActiveColumn()`. |
| `HasFeaturedColumn` | Same pattern for `is_featured` / `featured` scope. |
| `HasSortOrderColumn` | Scopes `orderBySortOrder`, `orderBySortOrderDesc`, `moveBefore` / `moveAfter`. |
| `HasTimestampColumns` | Datetime casts + optional soft deletes; override `shouldUseSoftDeletes()` to disable. |
| `HasSlugColumns` | Spatie `HasSlug` + `slugColumns` fillable; route key = slug. Override source/slug column methods. |
| `HasAuditColumns` | Fills audit IDs on create/update/delete when `Auth::check()`. Relationships need a resolvable user model (see config). |
| `HasMetaColumns` | Fillable + casts for `meta_title`, `meta_description`, `meta_keywords` (array), `meta_image`. Override column name methods if needed. |

---

## Blueprint macros

Registered when `eloquent-columns.register_macros` is `true`:

| Macro | Signature |
|-------|-----------|
| `activeColumn` | `($name = 'is_active', $default = true)` |
| `featuredColumn` | `($name = 'is_featured', $default = false)` |
| `sortOrderColumn` | `($name = 'sort_order', $default = 0)` |
| `slugColumns` | `($main = 'name', $slug = 'slug')` |
| `timestampColumns` | `($withSoftDeletes = true)` |
| `auditColumns` | `($created = 'created_by', $updated = 'updated_by', $deleted = 'deleted_by', $useUlid = true)` — set **`$useUlid` to `false`** for `foreignId` (bigint) columns. |
| `metaColumns` | `($title = 'meta_title', $description = 'meta_description', $keywords = 'meta_keywords', $image = 'meta_image')` |

---

## Example migration

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->slugColumns();
    $table->activeColumn();
    $table->featuredColumn();
    $table->sortOrderColumn();
    $table->timestampColumns();
    $table->auditColumns();
});
```

---

## Example model

```php
use Banulakwin\EloquentColumns\Concerns\HasActiveColumn;
use Banulakwin\EloquentColumns\Concerns\HasAuditColumns;
use Banulakwin\EloquentColumns\Concerns\HasFeaturedColumn;
use Banulakwin\EloquentColumns\Concerns\HasSlugColumns;
use Banulakwin\EloquentColumns\Concerns\HasSortOrderColumn;
use Banulakwin\EloquentColumns\Concerns\HasTimestampColumns;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasActiveColumn;
    use HasAuditColumns;
    use HasFeaturedColumn;
    use HasSlugColumns;
    use HasSortOrderColumn;
    use HasTimestampColumns;
}
```

---

## Package layout

```
config/eloquent-columns.php
src/Concerns/*.php
src/Macros/*.php
src/EloquentColumnsServiceProvider.php
```

---

## License

MIT
