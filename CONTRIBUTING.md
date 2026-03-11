# Contributing to Daily Habits

Thanks for your interest in contributing! This project is open source and welcomes contributions of all kinds.

## Getting Started

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone https://github.com/<your-username>/daily-habits.git
   cd daily-habits
   ```
3. Install dependencies:
   ```bash
   composer install
   npm install
   ```
4. Set up the environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   npm run build
   ```

## Development Workflow

Start the dev servers:
```bash
composer dev
```

### Quality Checks

Before submitting a PR, make sure all checks pass:

```bash
composer lint          # Format code with Pint
composer analyse       # PHPStan level 8
composer test          # Run tests
composer rector:check  # Rector dry-run
```

### Running Tests

```bash
# All tests
php artisan test

# Specific test
php artisan test --filter=HabitTest
```

## Pull Requests

1. Create a feature branch from `main`
2. Make your changes
3. Ensure all quality checks pass
4. Write tests for new functionality
5. Submit a PR with a clear description of your changes

## Code Style

- PHP code is formatted with [Laravel Pint](https://laravel.com/docs/pint)
- Static analysis with PHPStan at level 8
- PHPUnit for tests (not Pest)
- Livewire class-based components (not Volt)

## Forking

You're free to fork this repository and build your own app on top of it. If you do, a mention or link back is appreciated but not required.

## Questions?

Open an issue on GitHub if you have questions or run into problems.
