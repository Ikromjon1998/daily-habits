# Epic 1: Base Layout & App Shell

**Status:** Done

## Description

Create the mobile-first app shell with a bottom navigation bar, safe area insets, and a clean base Blade layout that all screens will extend.

## Scope

- Create a base Blade layout (`layouts/app.blade.php`) with:
  - Proper viewport meta tags for mobile
  - Safe area inset CSS variables (`--inset-top`, `--inset-bottom`)
  - Tailwind CSS with a warm, modern color palette (indigo/violet accent)
  - Dark background feel suitable for a habits app
- Create a bottom navigation component with 2 tabs:
  - **Today** (home icon) - shows today's habits
  - **Settings** (gear icon) - app info and notification permissions
- Set up routes for the two screens
- Create placeholder Livewire components for each tab
- Ensure the app looks polished on both iOS and Android

## Files to Create/Modify

- `resources/views/layouts/app.blade.php`
- `resources/css/app.css` (custom styles)
- `app/Livewire/Today.php` + view
- `app/Livewire/Settings.php` + view
- `routes/web.php`

## Acceptance Criteria

- [ ] App has a clean mobile layout with bottom navigation
- [ ] Safe area insets work correctly
- [ ] Two tabs navigate between screens
- [ ] Layout looks professional and screenshot-ready
