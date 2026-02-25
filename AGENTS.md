# Coding Agent Instructions

This is a **QC/validation project**, not a library. It consumes `evabee/schema-org-json-ld` as a Composer dependency and validates its JSON-LD output.

## Project structure

- `src/` — PHP scripts that instantiate schema types with realistic data and output JSON-LD
- `src/Fixtures/` — Realistic test data per schema type
- `tests/Unit/` — PHPUnit structural assertions on JSON-LD output
- `tests/E2E/` — TypeScript tests using `@adobe/structured-data-validator` for automated validation
- `results/` — Structured test results

## Key rules

- **Never modify the `evabee/schema-org-json-ld` package** — only consume it via Composer
- **Tab indentation** everywhere (matching main repo convention)
- **PHP 8.1+ minimum** — do not use features unavailable in 8.1
- **TypeScript** for all JavaScript/Node code — no `.js` files for new code
- **Bun** as the package manager and runtime — not npm/yarn
- **One PHPUnit test class per schema type** in `tests/Unit/`
- **One validation test per schema type** in `tests/E2E/`
- **Use TDD**: write failing tests first, then implement the code to make them pass

## Testing

- PHPUnit 10.x: `composer run test-unit`
- E2E validation: `bunx playwright test` or `bun run validate`
- CI only runs PHPUnit (`php-tests` job). E2E tests are the orchestrator's responsibility.

## Composer dependency

The package is pulled from a VCS repository:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/EvaLok/schema-org-json-ld"
        }
    ],
    "require": {
        "evabee/schema-org-json-ld": "dev-master"
    }
}
```

Run `composer update evabee/schema-org-json-ld` to pull the latest version.

## Validation approach

Use `@adobe/structured-data-validator` for programmatic validation of JSON-LD output against Google's structured data requirements. This runs locally — no browser automation needed.

- Generate JSON-LD from PHP usage scripts
- Wrap in minimal HTML with `<script type="application/ld+json">`
- Validate with the Adobe validator
- Store results in `results/` with: schema type, timestamp, package commit, pass/fail, errors, the JSON-LD tested
