---
name: qc-validation
description: Procedure for validating schema.org JSON-LD output from the evabee/schema-org-json-ld library. Covers generating output, running PHPUnit structural tests, running E2E validation with Adobe's structured data validator, and reporting failures cross-repo.
---

# QC Validation Workflow

How to validate JSON-LD output from the `evabee/schema-org-json-ld` library.

## Overview

This project consumes `evabee/schema-org-json-ld` as a Composer dependency and validates its output through two layers:

1. **PHPUnit structural tests** — assert JSON-LD shape, required fields, type correctness
2. **E2E validation** — validate against Google's requirements using `@adobe/structured-data-validator`

## Step 1: Update the library

Pull the latest version of the package:

```bash
composer update evabee/schema-org-json-ld
```

Check which commit was pulled:

```bash
grep -A5 '"evabee/schema-org-json-ld"' composer.lock
```

## Step 2: Discover new types

Check if the main repo has added schema types that this QC project doesn't cover yet:

```bash
bash tools/discover-types.sh
```

This compares the generate scripts in `src/` against the schema classes in the library.

## Step 3: Run the full test suite

```bash
bash tools/validate-all.sh
```

This runs both PHPUnit and E2E validation. Alternatively, run them separately:

```bash
php vendor/bin/phpunit -c phpunit.xml --testsuite=unit
```

```bash
bunx playwright test
```

## Step 4: Adding tests for a new schema type

### Generate script

Create `src/generate-{typename}.php` that instantiates the schema with realistic data and outputs JSON-LD:

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\{TypeName};

$schema = new {TypeName}(
    // Use realistic data, not placeholder text
    name: 'Concrete Example Name',
    // ...
);

echo JsonLdGenerator::SchemaToJson(schema: $schema);
```

### PHPUnit test

Create `tests/Unit/{TypeName}Test.php`:

- Test that JSON-LD output contains `@context` and correct `@type`
- Test all required fields are present
- Test optional fields appear when set
- Test null fields are omitted
- Test nested schemas serialize correctly
- Test enum values serialize to schema.org URLs

### E2E test

Create `tests/E2E/{typename}.spec.ts`:

- Generate JSON-LD via the PHP script
- Wrap in minimal HTML with `<script type="application/ld+json">`
- Validate with `@adobe/structured-data-validator`
- Assert zero errors (warnings are acceptable)

## Step 5: Reporting failures

If validation reveals a bug in the library, report it to the main repo.

1. Write the issue body to a file using the **Write** tool:

   Include: schema type, expected vs actual output, the specific validation error, and the library commit hash.

2. Create the cross-repo issue:

   ```bash
   bash tools/gh-post.sh create-issue "QC: {TypeName} validation failure" /tmp/qc-report.md qc-outbound
   ```

   This creates the issue on *this* repo with the `qc-outbound` label. The main repo's orchestrator monitors for these and creates corresponding `qc-inbound` issues.

## Key rules

- **Never modify the `evabee/schema-org-json-ld` package** — only consume it via Composer
- **Use realistic data** in generate scripts — not "Lorem ipsum" or "Test Name"
- **Tab indentation** everywhere (matching main repo convention)
- **One generate script, one PHPUnit test, one E2E test** per schema type
- **PHP 8.1+** minimum — do not use features unavailable in 8.1
- **TypeScript** for all E2E tests — no `.js` files
- **Bun** as the package manager and runtime — not npm/yarn

## Common mistakes

1. Using `npm` or `yarn` instead of `bun` — this project uses Bun exclusively
2. Writing placeholder data in generate scripts — use realistic business data
3. Modifying the library package directly — always go through Composer
4. Running E2E tests without `bun install` first — Playwright needs its dependencies
5. Forgetting to update `composer.lock` — always run `composer update evabee/schema-org-json-ld` before testing
