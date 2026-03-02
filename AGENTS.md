# Coding Agent Instructions

This is a **QC/validation project**, not a library. It consumes `evabee/schema-org-json-ld` as a Composer dependency and validates its JSON-LD output.

## Project structure

- `src/` — PHP scripts that instantiate schema types with realistic data and output JSON-LD
- `src/Fixtures/` — Realistic test data per schema type
- `tests/Unit/` — PHPUnit structural assertions on JSON-LD output
- `tests/E2E/` — TypeScript tests using `@adobe/structured-data-validator` for automated validation
- `results/` — Structured test results

## GitHub Pages

The repo publishes JSON-LD examples as static HTML pages via GitHub Pages. `scripts/build-pages.php` discovers all `src/generate-*.php` scripts, runs them, and writes HTML pages to `_site/`. The `_site/` directory is gitignored — pages are built by the `pages.yml` workflow on deploy. When you add a new generate script, the site automatically gains a new page.

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

## Helper tools (tools/)

Shell scripts for common operations. Invoke with `bash tools/<script>.sh` (not `./tools/`):

- `tools/session-init.sh <issue>` — Capture env info and post opening comment
- `tools/gh-post.sh comment <issue> <body-file>` — Post a comment from a file
- `tools/gh-post.sh create-issue <title> <body-file> [labels]` — Create issue from a file
- `tools/gh-post.sh close <issue>` — Close an issue
- `tools/validate-all.sh` — Run unit tests + E2E validation
- `tools/poll-repos.sh` — Check cross-repo communication status
- `tools/discover-types.sh` — Compare main repo types against our coverage
- `tools/git-commit.sh <message-file> [files...]` — Commit using a message file (avoids `$()` sandbox restriction)

## Coverage expansion tasks

The orchestrator dispatches Copilot to expand test coverage for uncovered schema types. Each task should:

- **Batch 4-6 related types** (e.g., all location types: Place, PostalAddress, GeoCoordinates, GeoShape)
- **Create for each type**:
  - `src/generate-<type>.php` — Instantiate with realistic data, output JSON-LD via `echo json_encode(...)`
  - `src/Fixtures/<Type>Fixture.php` — Realistic test data (if complex enough to warrant a fixture)
  - `tests/Unit/<Type>Test.php` — PHPUnit structural assertions on JSON-LD output
  - `tests/E2E/<type>.spec.ts` — Adobe structured data validator test
- **Follow existing patterns** — Look at `src/generate-product.php`, `tests/Unit/ProductTest.php`, and `tests/E2E/product.spec.ts` as reference
- **Use the installed package** — `use Evabee\SchemaOrgJsonLd\v1\<Type>;` (check actual class names in `vendor/evabee/schema-org-json-ld/php/src/v1/`)
- **Run `composer run test-unit`** before marking complete

## Claude Code sandbox notes

The Claude Code sandbox blocks certain shell constructs:
- **No `$()`**: Command substitution is blocked. Use helper scripts instead.
- **No `${}`**: Parameter expansion is blocked. Hardcode values or use scripts.
- **No `printenv`/`env`**: Environment variable inspection requires approval.
- **No `chmod`**: Permission changes may be blocked. Use `bash script.sh` instead.
- **Pipe/chain limits**: Complex pipes with `;` or `&&` may need approval.

When writing commands for the orchestrator, keep them simple: single commands, no substitutions, no special characters in arguments. For complex operations, use the tools/ scripts.
