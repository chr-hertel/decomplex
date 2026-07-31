# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

deComplex ([decomplex.me](https://decomplex.me)) is a slim Symfony 8.1 web app that calculates and diffs the
cyclomatic and cognitive complexity of two PHP code snippets side by side, and can share the comparison via a
short permalink. PHP 8.4, Postgres 15, Webpack Encore 7 + Tailwind 3 + CodeMirror 5 + jQuery 3 on the frontend.

## Commands

Prefix PHP commands with `symfony` (`symfony php`, `symfony console`, `symfony composer`) — the Symfony CLI
injects the Docker Compose database credentials into the environment. `bin/console` alone will not see them.

```bash
# Local setup (see README.md for the full sequence)
docker compose up -d                      # Postgres on host port 8432
symfony console doctrine:migration:migrate --no-interaction
symfony console doctrine:migration:migrate --env=test --no-interaction
npm run dev && symfony serve -d

# Full local check — mirrors CI plus npm audit and phpmd
bin/check

# Tests
symfony php vendor/bin/phpunit
symfony php vendor/bin/phpunit tests/DeComplex/ComplexityCalculatorTest.php
symfony php vendor/bin/phpunit --filter testCreatePermalink

# Static analysis / style (phpstan level 8 over src/ and tests/)
symfony php vendor/bin/phpstan analyse
symfony php vendor/bin/php-cs-fixer fix   # drop --dry-run to write
npm run check-style                       # prettier over assets/; npm run fix-style to write

# PHPMD lives in its own composer project — see "Dependency ceilings" below
symfony composer install --working-dir=tools/phpmd
symfony php tools/phpmd/vendor/bin/phpmd src ansi ruleset.xml --baseline-file phpmd-baseline.xml

# Frontend
npm run watch                             # dev build with watcher
npm run build                             # production build
```

PHPStan needs a warmed dev container (`var/cache/dev/App_KernelDevDebugContainer.xml`); run
`symfony console cache:warmup` first if analysis complains about a missing container.

## Dependency ceilings

`niels-de-blaauw/php-doc-check` — the library that supplies the two complexity metrics, so not optional —
pins `nikic/php-parser` to `^4`. That single constraint caps a surprising amount of the toolchain, and it is
the first thing to check when a `composer update` refuses to resolve:

- **PHPUnit stays on 10.x.** 11+ pull `sebastian/complexity`, which needs php-parser `^5`.
- **Infection is gone entirely.** 0.27 was the last release on php-parser `^4` and it caps
  `symfony/console` at `^7`, which blocked Symfony 8. Newer releases need php-parser `^5`, and infection
  resolves php-parser through the *project* autoloader — so neither a standalone composer install nor the
  phar avoids the collision. `infection.json.dist` is kept for whenever php-doc-check moves to `^5`.
- **PHPMD is installed from `tools/phpmd/`**, not the root `composer.json`. Its `pdepend` dependency caps
  `symfony/config` and `symfony/dependency-injection` at `^7` and would otherwise hold the app on Symfony 7.

Frontend majors deliberately held back, all of which need real UI verification rather than a version bump:
**jQuery 4** (verified to leave the CodeMirror editors uninitialised), **CodeMirror 6** (a rewrite —
`CodeMirror.fromTextArea` in `assets/js/editor.js` does not exist there) and **Tailwind 4** (CSS-first
config, and `@apply` from Sass needs `@reference`).

## Architecture

The whole domain lives in `src/DeComplex/`, wired by Symfony autowiring (`config/services.yaml`). Everything is
`final`, uses constructor promotion with `readonly`, and `declare(strict_types=1)`.

**Complexity calculation** — `ComplexityCalculator::analyze()` is the core. It hashes the code (`CodeHasher`,
md5), looks the hash up in `SnippetRepository` and returns the stored `Snippet` on a cache hit. Otherwise it
parses via nikic/php-parser (registered explicitly in `services.yaml` through `ParserFactory`) with a
`Collecting` error handler, and reduces the top-level statements through `NdB\PhpDocCheck\Metrics\*` from
`niels-de-blaauw/php-doc-check` to produce cyclomatic and cognitive values. A `null` AST throws
`ParserException` (→ 400 via `BadRequestHttpException`); collected parse errors throw `CalculationException`,
which is `JsonSerializable` and is returned directly as a 400 JSON body of `{line, message}` entries for the
editor to display inline.

**Persistence is a side effect of sharing, not of calculating.** `analyze()` returns an unmanaged `Snippet` for
new code; snippets only reach the database when `CodeDiffer::create()` persists a `Diff`, which cascades
persist to both sides. `Diff` carries a 6-char shortid primary key and a unique constraint on the snippet pair —
`CodeDiffer` catches `UniqueConstraintViolationException` and re-fetches the existing `Diff` so the same pair
always maps to the same permalink. Identical left/right code reuses one `Snippet` instance.

**Complexity levels** are derived in `Snippet::determineComplexityLevel()` (low < 4, moderate < 7, high < 10,
very-high < 100, else overkill); the level names become CSS classes (`complexity-level-*`) in the frontend.
`Snippet::jsonSerialize()` is the API response contract consumed by `assets/js/editor.js`.

**Controller** — one controller, `DeComplexController`, four routes: `GET /` and `GET /{id}` (6-char shortid,
resolved to a `Diff` by the param converter; a missing id renders the page with `missing_diff`), `POST /calculate`
(raw request *body* is the PHP code, not a form field or JSON), `POST /simplify`, and `POST /permalink`
(form fields `left`/`right`, returns the absolute permalink URL as a JSON string).

**AI simplification** — `ComplexitySimplifier` calls OpenAI (`openai-php/symfony`, gpt-4o-mini) with a system
prompt asking for simplified PHP, then strips markdown fences and re-prefixes `<?php`. The result is fed back
through `ComplexityCalculator` so `/simplify` returns the new code plus its complexity in one response. Requires
`OPENAI_API_KEY`; there is no test coverage for this path.

**Frontend** — `assets/js/editor.js` wraps each textarea in CodeMirror and owns the calculate/simplify AJAX
calls and DOM updates; `assets/js/complexity-diff.js` instantiates the two editors and handles permalink
create/copy. Templates hook JS by `js-*` classes (`#js-editor-left`, `#js-editor-right`) — the controller test
asserts against those selectors, so renaming them breaks tests.

## Testing

PHPUnit with `WebTestCase`; `tests/Controller/ComplexityDiffControllerTest.php` uses
`spatie/phpunit-snapshot-assertions` against the fixtures in `tests/fixtures/` (a messy and a clean version of
the same code). Snapshots live in `tests/Controller/__snapshots__/`; when complexity output legitimately
changes, delete the snapshot file and re-run to regenerate. Tests need the migrated `_test` database
(`doctrine.dbal.dbname_suffix` appends it) at port 8432 — see `.env.test`. The controller test renders the
real layout, so `npm run build` must have run at least once or it fails on the missing asset manifest.

`phpunit.xml.dist` sets `ignoreSuppressionOfDeprecations`, so vendor deprecations surface as test issues even
when the triggering code silences them — a run reporting "OK, but there were issues" is worth reading.

## Deployment

Push to `main` triggers `.github/workflows/deployment.yml`, which runs Deployer (`deploy.php`) against
decomplex.me: composer install without dev, `npm clean-install && npm run build`, `dotenv:dump`, cache clear,
and `database:migrate`. PRs run `.github/workflows/pipeline.yml` (validate, lint yaml/twig/container, prod
cache warmup, php-cs-fixer, prettier, phpstan, migrate test db, frontend build, phpunit).

The deploy action installs its own Deployer via the `deployer-version` pin rather than using the locked one,
so that pin and the `deployer/deployer` constraint in `composer.json` have to be bumped together.
