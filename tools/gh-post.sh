#!/usr/bin/env bash
# gh-post.sh — Post a comment or create an issue, reading body from a file
# This avoids shell escaping issues with $(), ${}, quotes, and special chars.
#
# Usage:
#   tools/gh-post.sh comment <issue-number> <body-file>
#   tools/gh-post.sh create-issue <title> <body-file> [label1,label2]
#   tools/gh-post.sh close <issue-number>
#
# The body-file is a plain text file containing the comment/issue body.

set -euo pipefail

REPO="EvaLok/schema-org-json-ld-qc"
ACTION="${1:?Usage: gh-post.sh <comment|create-issue|close> ...}"

case "$ACTION" in
  comment)
    ISSUE="${2:?Usage: gh-post.sh comment <issue-number> <body-file>}"
    BODY_FILE="${3:?Usage: gh-post.sh comment <issue-number> <body-file>}"
    gh issue comment "$ISSUE" -R "$REPO" -F "$BODY_FILE"
    ;;
  create-issue)
    TITLE="${2:?Usage: gh-post.sh create-issue <title> <body-file> [labels]}"
    BODY_FILE="${3:?Usage: gh-post.sh create-issue <title> <body-file> [labels]}"
    LABELS="${4:-}"
    if [ -n "$LABELS" ]; then
      gh issue create -R "$REPO" --title "$TITLE" -F "$BODY_FILE" --label "$LABELS"
    else
      gh issue create -R "$REPO" --title "$TITLE" -F "$BODY_FILE"
    fi
    ;;
  close)
    ISSUE="${2:?Usage: gh-post.sh close <issue-number>}"
    gh issue close "$ISSUE" -R "$REPO"
    ;;
  *)
    echo "Unknown action: $ACTION" >&2
    echo "Usage: gh-post.sh <comment|create-issue|close> ..." >&2
    exit 1
    ;;
esac
