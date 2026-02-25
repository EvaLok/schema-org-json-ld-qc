#!/usr/bin/env bash
# git-commit.sh — Commit using a message file (avoids $() sandbox restriction)
#
# Usage:
#   tools/git-commit.sh <message-file> [files-to-stage...]
#
# If no files are specified, commits whatever is already staged.
# If files are specified, stages them before committing.
#
# Examples:
#   # Write message with the Write tool, then:
#   bash tools/git-commit.sh /tmp/commit-msg.txt
#   bash tools/git-commit.sh /tmp/commit-msg.txt src/generate-article.php tests/Unit/ArticleTest.php
#   bash tools/git-commit.sh /tmp/commit-msg.txt .   # stage everything

set -euo pipefail

MSG_FILE="${1:?Usage: git-commit.sh <message-file> [files-to-stage...]}"

if [ ! -f "$MSG_FILE" ]; then
  echo "Error: message file not found: $MSG_FILE" >&2
  exit 1
fi

shift

# Stage files if specified
if [ $# -gt 0 ]; then
  git add "$@"
fi

git commit -F "$MSG_FILE"
