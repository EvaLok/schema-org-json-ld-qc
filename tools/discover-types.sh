#!/usr/bin/env bash
# discover-types.sh — Compare main repo schema types against our coverage
#
# Usage: tools/discover-types.sh
#
# Lists all schema types in the main repo and shows which ones we cover.

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"

echo "=== Schema types in main repo ==="
MAIN_TYPES=$(gh api "repos/EvaLok/schema-org-json-ld/contents/src/v1/Schema" \
  --jq '.[].name' | sed 's/\.php$//' | sort)
echo "$MAIN_TYPES"

echo ""
echo "=== Our generate scripts ==="
COVERED=$(ls "$PROJECT_ROOT"/src/generate-*.php 2>/dev/null \
  | sed 's|.*/generate-||; s|\.php$||' | sort)
echo "$COVERED"

echo ""
echo "=== Uncovered types (in main repo but no generate script) ==="
comm -23 <(echo "$MAIN_TYPES") <(echo "$COVERED" | while read -r name; do
  # Convert kebab-case to PascalCase for comparison
  echo "$name" | sed -r 's/(^|-)(\w)/\U\2/g'
done | sort)
