<laravel-boost-guidelines>

# Daily Habits — AI Development Guidelines

## Stack & Versions

- PHP 8.4 / Laravel 12 / Livewire 4 / Tailwind CSS 4
- NativePHP Mobile v3 + `ikromjon/nativephp-mobile-local-notifications` v1.7.0
- PHPStan (Larastan) level 8 / Pint / Rector
- SQLite database, no authentication, no API

## Quality Gates (Before Every Commit)

```bash
composer lint        # Format with Pint
composer analyse     # PHPStan level 8
composer rector:check # Rector dry-run
```

All must pass before notifying the user to commit.

## Laravel Boost MCP Tools

- Use `search-docs` for version-specific Laravel/Livewire/Tailwind docs before making changes.
- Use `tinker` to debug or query Eloquent models.
- Use `database-schema` to inspect table structure before writing migrations.
- Use `list-artisan-commands` to check available artisan command parameters.

## PHP Rules

- Explicit return types on all methods.
- PHP 8 constructor property promotion.
- Curly braces for all control structures.
- PHPDoc `@return` with generics on Eloquent relationships: `@return HasMany<Model, $this>`.
- Prefer PHPDoc blocks over inline comments.

## Laravel Rules

- Use `php artisan make:*` with `--no-interaction` to create files.
- Prefer Eloquent over `DB::` facade. Use eager loading to prevent N+1.
- Use `whereDate()` for date column queries (Carbon v3 stores full datetime).
- Carbon v3 `diffInDays()` returns signed values — use `abs()` when comparing.
- Use `config()` helper, never `env()` outside config files.
- Laravel 12 structure: middleware in `bootstrap/app.php`, no Kernel.php.

## Livewire Rules

- Class-based components only (not Volt).
- Return type `View` on `render()` methods.
- Use `wire:navigate` for SPA navigation.
- Use `#[OnNative(EventClass::class)]` for native event listeners.

## NativePHP Mobile Rules

- Register plugins in `app/Providers/NativeServiceProvider.php` `plugins()` method.
- Safe area insets via CSS: `var(--inset-top)`, `var(--inset-bottom)`.
- Notifications require native build — do not work with Jump.
- Use `@mobile` / `@web` Blade directives for platform-specific rendering.

## Tailwind CSS Rules

- Tailwind v4 syntax: `@theme` in CSS, no tailwind.config.js.
- Dark theme default: `bg-gray-950`, white text.
- Check existing patterns before adding new utility classes.

## Code Formatting

- Run `vendor/bin/pint --dirty` after modifying PHP files.

## Testing

- PHPUnit (not Pest). Use `php artisan make:test --phpunit`.
- Run related tests after changes: `php artisan test --compact --filter=testName`.
- Do not remove test files without approval.

## Project Conventions

- Follow existing code patterns. Check sibling files before creating new ones.
- Do not change dependencies without approval.
- Do not create documentation files unless explicitly requested.
- Be concise — focus on what's important.

## Epic Workflow

1. Read the epic file in `plan/` before starting.
2. Implement the scope.
3. Run quality gates.
4. Update epic status in both the epic file and `plan/README.md`.
5. Notify user to commit — do NOT commit yourself.

</laravel-boost-guidelines>
