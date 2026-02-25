#!/usr/bin/env bash
# session-init.sh — Capture environment info and post opening comment
# Usage: tools/session-init.sh <issue-number>
#
# Outputs environment info to stdout as key=value pairs.
# Posts an opening comment to the specified issue.

set -euo pipefail

ISSUE_NUM="${1:?Usage: session-init.sh <issue-number>}"
REPO="EvaLok/schema-org-json-ld-qc"

TIMESTAMP=$(date -u '+%Y-%m-%dT%H:%M:%SZ')
RUN_ID="${GITHUB_RUN_ID:-local}"
RUN_URL="${GITHUB_SERVER_URL:-https://github.com}/${GITHUB_REPOSITORY:-$REPO}/actions/runs/${RUN_ID}"

# Output env info
echo "timestamp=$TIMESTAMP"
echo "run_id=$RUN_ID"
echo "run_url=$RUN_URL"

# Post opening comment
gh issue comment "$ISSUE_NUM" -R "$REPO" -b "## QC Orchestrator Cycle — Issue #${ISSUE_NUM}

**Model**: Claude Opus 4.6
**Timestamp**: ${TIMESTAMP}
**Run ID**: [${RUN_ID}](${RUN_URL})

Starting cycle. Following startup checklist..."
