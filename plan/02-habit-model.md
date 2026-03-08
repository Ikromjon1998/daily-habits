# Epic 2: Habit Model & Database

**Status:** Not Started

## Description

Create the Habit model, migration, and HabitCompletion model to track daily habits and their completion status.

## Scope

- Create `habits` migration with fields:
  - `id`, `name`, `description` (nullable), `emoji` (icon), `reminder_time` (time), `frequency` (daily/weekdays/weekends), `is_active` (boolean), `created_at`, `updated_at`
- Create `habit_completions` migration:
  - `id`, `habit_id` (foreign key), `completed_at` (date), `created_at`, `updated_at`
- Create `Habit` model with relationships and helper methods
- Create `HabitCompletion` model
- Add a seeder with sample habits for development

## Files to Create

- `database/migrations/xxxx_create_habits_table.php`
- `database/migrations/xxxx_create_habit_completions_table.php`
- `app/Models/Habit.php`
- `app/Models/HabitCompletion.php`
- `database/seeders/HabitSeeder.php`

## Acceptance Criteria

- [ ] Migrations run without errors
- [ ] Models have correct relationships
- [ ] Seeder creates realistic sample data
- [ ] Habit model has helpers: `isCompletedToday()`, `currentStreak()`, `shouldShowToday()`
