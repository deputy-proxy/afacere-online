# AI Development Rules

This document is the project-level CI and implementation contract for AI-assisted development in **afacere.online**.

The authoritative sources are, in order of priority:

1. The actual GitHub Actions workflow configuration in `.github/workflows/`.
2. `composer.json` and the dependency lockfile.
3. Tool configuration files such as `pint.json`, `phpstan.neon`, `phpunit.xml`, and framework configuration.
4. Existing production code and tests, when they establish repository conventions.
5. This document, which summarizes the rules above for repeatable AI-assisted implementation.

This document must not be used to override the actual CI configuration. When repository configuration changes, this document should be updated accordingly.

## Repository contract

afacere.online is a Laravel application for Romanian entrepreneurs. Its product workflow is centered on helping a business understand its current state, identify priorities, take action, record progress, and return to reassess.

The repository README, documentation under `docs/`, implementation issues, repository configuration and CI are authoritative for product and technical decisions. Do not invent unresolved business rules when the repository already records an authoritative decision.

The current application stack is Laravel 13, Filament 5, Livewire 4 and PHP 8.4. The repository uses Blade/Tailwind/Vite for the frontend and Pest for automated tests.

## CI workflow contract

The repository currently has one primary GitHub Actions workflow: `.github/workflows/tests.yml`.

It runs for:

- pushes to `main`;
- every pull request.

The CI job runs on `ubuntu-latest` and performs the following sequence:

1. Checkout the repository.
2. Set up PHP `8.4` with Composer v2 and no coverage collection.
3. Set up Node.js `22`.
4. Run `composer setup`.
5. Run `composer ci:check`.

The `composer ci:check` script delegates to the repository's test/quality pipeline, which runs configuration clearing, Pint lint checking, PHPStan/Larastan static analysis, and the Laravel test suite.

Therefore, a pull request is not CI-safe merely because the PHP code is logically correct. The implementation must be compatible with the complete setup, formatting, static-analysis, database/setup, frontend build and test sequence above.

The CI workflow is authoritative. If it differs from this document, follow the workflow and update this document.

## Runtime and framework contract

The application currently declares:

- PHP: `^8.4`.
- Laravel Framework: `^13.17`.
- Filament: `^5.0`.
- Livewire: `^4.1`.
- Pest: `^5.1` with `pest-plugin-laravel` `^5.0`.
- Laravel Pint: `^1.27`.
- Larastan: `^3.9`.
- Node.js in CI: `22`.

The Composer setup script installs dependencies, creates `.env` when needed, generates the application key, migrates the database, installs npm dependencies and builds frontend assets.

Use only APIs and language features available in the versions actually declared by the project.

Do not assume APIs from older or newer framework versions merely because they are familiar.

## Required quality checks

The repository quality pipeline includes:

- `composer lint:check`, which runs `pint --parallel --test` and must pass without modifying files.
- `composer types:check`, which runs `phpstan analyse` using the repository's `phpstan.neon` configuration.
- `php artisan test`, which runs the Laravel/Pest test suite configured by the project.

The `composer ci:check` script is the CI entry point and delegates to the repository test pipeline. The exact GitHub Actions workflow remains authoritative.

`composer lint` is the formatter command and is not itself the CI check.

Never claim that a quality check passed unless it was actually executed and its result is available. Static review can predict failures, but it does not establish a passing result.

## Code style and lint contract

Formatting is governed by Laravel Pint using the repository's `pint.json` configuration:

```json
{
    "preset": "laravel"
}
```

The CI lint check runs Pint in test mode.

Before committing:

- Match the Laravel Pint style used by the repository.
- Keep imports correct and ordered according to project conventions.
- Do not introduce unused imports, variables, parameters, properties, methods or classes.
- Avoid unreachable code and redundant branches.
- Match the surrounding code's naming, spacing, visibility and declaration conventions.
- Review every changed PHP file, not only the lines that implement the issue.
- Do not rely on Pint to silently repair the implementation after it has been committed.

Do not modify formatting configuration merely to make an implementation pass lint.

## PHPStan / Larastan contract

The CI type check runs `phpstan analyse` using `phpstan.neon`.

PHPStan is configured at **level 7** and analyzes:

- `app/`
- `bootstrap/app.php`
- `config/`
- `database/`
- `routes/`

Larastan and Carbon extension configuration is loaded through `phpstan.neon`.

Before committing, perform a static analysis review of every changed file and its relevant call sites. Pay particular attention to:

- missing or incorrect return types;
- nullable values used as non-nullable values;
- incorrect property and method types;
- undefined methods or properties;
- incorrect method arguments;
- array and collection value types;
- generic collection assumptions;
- model relationship types;
- factory return types;
- request/input values treated as strongly typed without validation;
- incorrect interface or contract implementations;
- impossible comparisons and unreachable branches;
- incorrect namespace/import references;
- framework APIs whose signatures differ between Laravel versions;
- test doubles whose types do not match the real dependency.

Do not suppress PHPStan findings or weaken the analysis level merely to make CI pass.

Use repository-consistent typing and annotations. Add an annotation only when it accurately describes the runtime contract.

## Test contract

The CI test command is `php artisan test`.

Tests are configured in `phpunit.xml` with two suites:

- `tests/Unit`
- `tests/Feature`

The test environment uses SQLite in-memory storage and sets the application environment to `testing`.

Before committing, review every changed behavior for appropriate test coverage.

Tests should:

- follow the existing Pest/Laravel testing style;
- reuse existing factories, helpers and fixtures;
- verify important success and failure paths;
- verify authorization and validation where applicable;
- verify relevant database state;
- be deterministic and isolated;
- avoid dependence on test execution order;
- avoid brittle implementation-detail assertions;
- use mocks only where they reflect actual application boundaries.

Do not change or weaken an existing test merely to accommodate an incorrect implementation.

When a schema, model, relationship or factory changes, inspect all directly affected tests and fixtures for compatibility.

Because CI runs `composer setup` before the quality/test pipeline, database migrations and frontend build/setup behavior are also part of the practical CI contract.

## Laravel implementation contract

Use the project's established Laravel architecture and existing patterns.

Before introducing a new abstraction, first determine whether an existing service, action, domain object, request, policy, model method, query scope, component or helper already provides the required behavior.

For every framework API used:

1. Check the version declared in `composer.json`.
2. Inspect existing repository usage of that API where possible.
3. Prefer patterns already used by the project.

Do not introduce behavior based solely on memory of a different Laravel version.

For Filament and Livewire code, inspect existing resources, pages, components, forms, tables, actions and computed/state patterns before introducing a new approach. Keep business rules in appropriate application/domain services and policies rather than duplicating them in UI components.

## Domain and product contract

afacere.online's central progression is:

```text
Create business
  ↓
Evaluate
  ↓
Diagnose
  ↓
Prioritize
  ↓
Act
  ↓
Record progress
  ↓
Monitor
  ↓
Reassess
```

The primary entrepreneur-facing question is **"What should I do next?"**.

Important domain behavior should remain contextual to the business, stage, goals, history, evaluation findings, priorities, actions and relevant ecosystem capabilities. AI may assist analysis and explanation, but deterministic authorization, eligibility and core business rules remain server-side and deterministic.

Historical evaluations, action plans, recommendations, commercial state and other audit-sensitive records should remain traceable where the existing domain model requires history.

Do not turn the application into a generic AI chatbot, generic content portal or collection of disconnected tools. New functionality should fit the established business-progression model and the issue's approved implementation plan.

## AI implementation contract

AI is an assistance layer, not the product itself. AI-related functionality may support evaluation interpretation, explanations, recommendations, Action Plans, Guide personalization, matching, summaries, search and internal workflows where the product design calls for it.

Important AI rules:

1. AI output is not guaranteed business advice.
2. Important recommendations should retain relevant source/context where the domain supports it.
3. Important AI outputs should be stored for auditability where required by the existing design.
4. Prompts and relevant configuration should be versioned where the feature requires it.
5. AI suggestions remain separate from user-confirmed decisions.
6. Users can reject, edit or accept recommendations where the workflow supports those states.
7. Core domain logic is not coupled unnecessarily to one AI vendor.
8. AI usage and cost should be tracked where the existing architecture provides for it.
9. Deterministic business rules remain deterministic.
10. Structured AI output must be validated before entering domain workflows.

Do not introduce an AI dependency merely because an AI solution appears convenient. First determine whether deterministic application logic is more appropriate.

## Security, privacy and authorization

Authorization is server-side and scoped to the relevant business/user context. Business data is private by default.

Experts, Marketplace providers, community participants and other actors should receive only information intentionally exposed through the relevant workflow. Administrative access must remain authorized and auditable where it affects user data, recommendations, commercial state or trust-sensitive records.

Sensitive operations such as data export, deletion, payment callbacks and commercial state transitions require explicit server-side rules.

Never rely on UI visibility alone for authorization.

## Scope discipline

An issue implementation must be narrowly scoped.

Do not:

- refactor unrelated code;
- rename unrelated symbols;
- introduce speculative abstractions;
- add dependencies without necessity;
- alter CI configuration to conceal failures;
- perform unrelated cleanup;
- modify unrelated tests;
- broaden the issue beyond the approved implementation plan.

Prefer the smallest change that satisfies the issue while preserving existing behavior.

If the implementation plan conflicts with repository configuration or existing authoritative decisions, stop and resolve the conflict through the repository's issue/documentation process rather than silently inventing a new rule.

## Static CI simulation before commit

When local execution of lint, PHPStan or tests is unavailable, the implementation process must compensate with a static CI review.

Before committing, review the implementation in this exact order:

1. Compare the changed files against `.github/workflows/tests.yml` and repository tool configuration.
2. Confirm the implementation does not depend on a runtime, PHP, Node, Laravel or package version different from CI.
3. Review every changed file for Pint violations.
4. Review every changed file against PHPStan level 7 expectations.
5. Review every changed or added test for Pest/Laravel and `phpunit.xml` compatibility.
6. Review migrations, factories, models and database assumptions together.
7. Review routes, controllers, requests, policies and authorization boundaries together.
8. Review Filament resources/pages and Livewire components against existing patterns and server-side authorization.
9. Review imports, namespaces, method signatures and return types.
10. Review test isolation, fixtures and database state.
11. Review frontend/build-related changes against the Node.js `22` setup where applicable.
12. Inspect the complete final diff for accidental or unrelated changes.

Then perform an adversarial second pass:

> What is the most likely reason GitHub CI could reject this change even though the implementation appears functionally correct?

Consider separately:

- a formatting failure;
- a PHPStan level 7 failure;
- a test discovery or syntax failure;
- a failing assertion;
- a migration/schema/factory failure;
- a framework-version/API failure;
- a setup, asset-build or environment failure;
- a Filament/Livewire state or authorization failure.

Fix every issue that can be identified statically before committing.

This review is a prediction exercise, not a substitute for CI.

## Commit and pull request contract

Before committing:

- ensure the branch is the issue-specific branch required by the project workflow;
- ensure the implementation is limited to the issue scope;
- complete the static CI simulation above;
- do not represent unexecuted checks as passing.

Pull requests for issue implementations should use the project's established naming convention.

After pushing:

- inspect the actual GitHub CI result;
- if CI fails, identify the root cause rather than patching only the first symptom;
- fix the smallest correct change;
- repeat the static CI simulation;
- push the fix and wait for CI again;
- do not stop until all required checks are green or the work is explicitly stopped.

A green CI result is required before executable implementation work is considered complete. Do not weaken CI, skip tests, suppress static-analysis findings or alter configuration merely to obtain a green result.

## Documentation contract

The repository's README and `docs/` contain product, architecture, validation and operational documentation. Implementation-significant decisions should be reflected in the appropriate documentation or issue when required by the project workflow.

Do not represent source-code presence as evidence that an external or operational capability has actually been verified. In particular, deployment, provider integrations, backups/restores, accessibility reviews, load tests and other infrastructure/manual controls require the appropriate evidence.

When documentation describes a phase or launch gate, preserve the distinction between implemented source code and validated operational evidence.

## Change control

If the repository's CI workflow, tool configuration, framework version or test configuration changes, update this document in the same change whenever practical.

The actual repository configuration always wins over this summary.
