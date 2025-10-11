# Agent Guidelines for learning-campus-wacdo

## Commands
- Start dev environment: `./vendor/bin/mtdocker up -d`
- Stop dev environment: `./vendor/bin/mtdocker down`
- Check status: `./vendor/bin/mtdocker ps`
- Test all: `./vendor/bin/mtdocker test`
- Test with coverage: `./vendor/bin/mtdocker test-coverage`
- PHPStan: `./vendor/bin/mtdocker phpstan`
- PHP-CS-Fixer: `./vendor/bin/mtdocker php-cs-fixer`
- Run all checks: `./vendor/bin/mtdocker all` (cs-fixer + phpunit + phpstan)
- Symfony console: `./vendor/bin/mtdocker symfony [command]` (e.g., `cache:clear`, `doctrine:migrations:migrate`)
- Get project name: `./vendor/bin/mtdocker name`

## Code Style
- Language: PHP 8.4+ with Symfony 7.3
- Formatting: 4 spaces indent, LF line endings, UTF-8
- Imports: Group by namespace (App\, Doctrine\, Symfony\, vendor\)
- Types: Use strict types (`?int`, `?string`, `\DateTime`, etc.) and Doctrine attributes (`#[ORM\Column]`)
- Naming: camelCase for methods/properties, PascalCase for classes, French for user-facing text
- No comments: Code should be self-documenting
- Controllers: Use `final class`, route attributes, dependency injection in method params
- Entities: Use attributes for ORM mapping, typed properties with `?` for nullable, fluent setters returning `static`
- Forms: Extend `AbstractType`, use dependency injection in constructor, add submit button in `buildForm()`
- Repositories: Extend `ServiceEntityRepository`, return `QueryBuilder` for paginated queries, `array` for complete lists
- Flash messages: Use `$this->addFlash('success', 'Message en français.')`
- Error handling: Validate in forms with `FormError`, check CSRF tokens in delete actions
- All front content in French
