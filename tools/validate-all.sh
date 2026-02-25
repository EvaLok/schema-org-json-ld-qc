#!/usr/bin/env bash
# validate-all.sh — Run unit tests and E2E validation, output structured results
#
# Usage: tools/validate-all.sh
#
# Runs PHPUnit unit tests and bun E2E validation in sequence.
# Outputs a summary to stdout and saves detailed results.

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$PROJECT_ROOT"

echo "=== Unit Tests ==="
UNIT_RESULT=0
php vendor/bin/phpunit -c phpunit.xml --testsuite=unit 2>&1 || UNIT_RESULT=$?

echo ""
echo "=== E2E Validation ==="
E2E_RESULT=0
bun run scripts/validate.ts src/generate-*.php 2>&1 || E2E_RESULT=$?

echo ""
echo "=== Summary ==="
if [ "$UNIT_RESULT" -eq 0 ]; then
  echo "Unit tests: PASS"
else
  echo "Unit tests: FAIL (exit code $UNIT_RESULT)"
fi

if [ "$E2E_RESULT" -eq 0 ]; then
  echo "E2E validation: PASS"
else
  echo "E2E validation: FAIL (exit code $E2E_RESULT)"
fi

# Exit with failure if either failed
if [ "$UNIT_RESULT" -ne 0 ] || [ "$E2E_RESULT" -ne 0 ]; then
  exit 1
fi
