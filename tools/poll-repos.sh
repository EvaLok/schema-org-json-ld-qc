#!/usr/bin/env bash
# poll-repos.sh — Poll both repos for cross-repo communication status
#
# Usage: tools/poll-repos.sh
#
# Checks:
# 1. Main repo qc-outbound issues (validation requests TO us)
# 2. Main repo qc-inbound issues (acknowledgments OF our reports)
# 3. Our open qc-outbound issues
# 4. Our open qc-inbound issues
# 5. Open input-from-eva issues
# 6. Open PRs on our repo

set -euo pipefail

MAIN_REPO="EvaLok/schema-org-json-ld"
QC_REPO="EvaLok/schema-org-json-ld-qc"

echo "=== Main Repo: qc-outbound (requests TO us) ==="
gh api "repos/$MAIN_REPO/issues?labels=qc-outbound&state=open&creator=EvaLok&sort=created&direction=asc" \
  --paginate --jq '.[] | "  #\(.number): \(.title) (\(.created_at))"' || echo "  (none or error)"

echo ""
echo "=== Main Repo: qc-inbound (acks OF our reports) ==="
gh api "repos/$MAIN_REPO/issues?labels=qc-inbound&state=open&creator=EvaLok&sort=created&direction=asc" \
  --paginate --jq '.[] | "  #\(.number): \(.title) (\(.created_at))"' || echo "  (none or error)"

echo ""
echo "=== QC Repo: Open qc-outbound issues ==="
gh api "repos/$QC_REPO/issues?labels=qc-outbound&state=open" \
  --jq '.[] | "  #\(.number): \(.title)"' || echo "  (none or error)"

echo ""
echo "=== QC Repo: Open qc-inbound issues ==="
gh api "repos/$QC_REPO/issues?labels=qc-inbound&state=open" \
  --jq '.[] | "  #\(.number): \(.title)"' || echo "  (none or error)"

echo ""
echo "=== QC Repo: input-from-eva ==="
gh api "repos/$QC_REPO/issues?labels=input-from-eva&state=open&creator=EvaLok" \
  --jq '.[] | "  #\(.number): \(.title)"' || echo "  (none or error)"

echo ""
echo "=== QC Repo: Open PRs ==="
gh pr list -R "$QC_REPO" --state open --json number,title,author \
  --jq '.[] | "  #\(.number): \(.title) (by \(.author.login))"' || echo "  (none or error)"

echo ""
echo "=== Audit Repo: audit-outbound (recommendations TO us) ==="
gh api "repos/EvaLok/schema-org-json-ld-audit/issues?labels=audit-outbound&state=open&creator=EvaLok&sort=created&direction=asc" \
  --paginate --jq '.[] | "  #\(.number): \(.title) (\(.created_at))"' || echo "  (none or error)"

echo ""
echo "=== Package commit ==="
COMMIT=$(grep -A5 '"evabee/schema-org-json-ld"' composer.lock | grep '"reference"' | head -1 | grep -oP '[a-f0-9]{40}')
echo "  $COMMIT"
