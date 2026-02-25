# schema-org-json-ld-qc

Quality control and validation for the [evabee/schema-org-json-ld](https://github.com/EvaLok/schema-org-json-ld) PHP package. This is an independent consumer project that exercises the library and validates its JSON-LD output against Google's structured data requirements.

**Live examples:** [evalok.github.io/schema-org-json-ld-qc](https://evalok.github.io/schema-org-json-ld-qc/) — browsable HTML pages with `<script type="application/ld+json">` tags generated from the package output.

## What this repo does

- Imports `evabee/schema-org-json-ld` via Composer as a real consumer would
- Generates JSON-LD output for each supported schema type using realistic test data
- Runs PHPUnit tests asserting structural correctness of the output
- Validates JSON-LD against Google's requirements using [@adobe/structured-data-validator](https://www.npmjs.com/package/@adobe/structured-data-validator)
- Reports failures back to the main repo via a cross-repo issue protocol

## Project structure

| Path | Purpose |
|---|---|
| `src/generate-*.php` | Usage scripts — instantiate schema types and output JSON-LD |
| `src/Fixtures/` | Realistic test data per schema type |
| `tests/Unit/` | PHPUnit structural assertions on JSON-LD output |
| `tests/E2E/` | TypeScript validation tests using Adobe's structured data validator |
| `scripts/build-pages.php` | Builds static HTML pages from generate scripts for GitHub Pages |
| `results/` | Structured test results |
| `tools/` | Helper shell scripts for orchestrator operations |

## Running tests

```bash
# Install dependencies
composer install
bun install

# PHPUnit
composer run test-unit

# E2E validation
bunx playwright test
```

## GitHub Pages

The [live site](https://evalok.github.io/schema-org-json-ld-qc/) is built automatically on push to master. Each page wraps the JSON-LD output from a generate script in a valid `<script type="application/ld+json">` tag, making the pages suitable for URL-based validation tools.

## Autonomous operation

This repo is maintained by an autonomous QC orchestrator (Claude Opus 4.6) that runs on a cron schedule via GitHub Actions. It discovers new schema types, dispatches test-writing tasks to Copilot, validates output, and reports problems to the main repo. See [JOURNAL.md](JOURNAL.md) for its working log.

## License

MIT
