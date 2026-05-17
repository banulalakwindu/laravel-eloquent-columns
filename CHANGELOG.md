# Changelog

All notable changes to `banulakwin/laravel-eloquent-columns` will be documented in this file.

## 1.0.0 — 2026-05-17

### Added
- Blueprint macros: `activeColumn`, `auditColumns`, `featuredColumn`, `slugColumns`, `sortOrderColumn`, `timestampColumns`, `metaColumns`.
- Eloquent concerns: `HasActiveColumn`, `HasAuditColumns`, `HasFeaturedColumn`, `HasSlugColumns`, `HasSortOrderColumn`, `HasTimestampColumns`, `HasMetaColumns`.
- Config `eloquent-columns.register_macros` to disable macro registration.
- Publish tag: `eloquent-columns-config`.
- PHPUnit test suite (68 tests) with Orchestra Testbench.
- GitHub Actions CI workflow (tests, Pint, PHPStan).
- Laravel Pint code style configuration.
- PHPStan static analysis (level max).
