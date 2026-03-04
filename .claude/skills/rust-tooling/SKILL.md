---
name: rust-tooling
description: How to create Rust tools for the QC orchestrator, add crates to the workspace, and write shell wrappers. Use when building new tools or extending existing ones.
user-invocable: false
---

# Creating Rust Tools

The QC orchestrator can build its own compiled tools to automate repeated patterns. Tools live in a Cargo workspace at `tools/rust/`.

## Workspace structure

```
tools/
  my-tool              # shell wrapper (extensionless, executable)
  rust/
    Cargo.toml         # workspace root: members = ["crates/*"]
    Cargo.lock         # committed for reproducible builds
    crates/
      my-tool/         # one crate per tool
        Cargo.toml
        src/
          main.rs
```

## Adding a new tool

### 1. Create the crate

```bash
mkdir -p tools/rust/crates/my-tool/src
```

### 2. Write `Cargo.toml`

Use the Write tool to create `tools/rust/crates/my-tool/Cargo.toml`:

```toml
[package]
name = "my-tool"
version = "0.1.0"
edition = "2021"
description = "What this tool does"

[dependencies]
clap = { version = "4", features = ["derive"] }
serde_json = "1"
```

The workspace `members = ["crates/*"]` glob automatically picks up new crates.

### 3. Write the source

Use the Write tool to create `tools/rust/crates/my-tool/src/main.rs` with a `clap` CLI.

Convention: accept `--repo-root <path>` as the first argument for locating repo files.

### 4. Build

```bash
cargo build --release --manifest-path tools/rust/Cargo.toml
```

### 5. Create the shell wrapper

Use the Write tool to create `tools/my-tool` (extensionless):

```bash
#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
BINARY="$SCRIPT_DIR/rust/target/release/my-tool"

if [ ! -x "$BINARY" ]; then
    echo "Pre-built binary not found, building..." >&2
    cargo build --release --manifest-path "$SCRIPT_DIR/rust/Cargo.toml" >&2
fi

exec "$BINARY" --repo-root "$REPO_ROOT" "$@"
```

### 6. Test

```bash
bash tools/my-tool --help
```

## Conventions

- **Use `clap` derive** for CLI argument parsing
- **Accept `--repo-root`** to locate repo files relative to the workspace root
- **Shell wrappers are extensionless** — invoked as `bash tools/my-tool`
- **Commit `Cargo.lock`** for reproducible CI builds
- **Pre-build in CI** — the orchestrator workflow builds all tools before the session starts
- **Keep dependencies minimal** — each tool should be focused and fast to compile
- **`serde_json`** is the standard crate for JSON parsing

## When to create a tool

Create a tool when you notice:
- A multi-step manual process repeated across 2+ sessions
- Complex JSON processing that's error-prone with manual jq queries
- Verification or validation checks that should be automated
- Data extraction from state files or API responses
- Any task blocked by sandbox restrictions on existing tools

Do NOT create a tool for one-off tasks — use Write/Edit/Grep directly.

## Permissions

The orchestrator has `Bash(cargo *)` and `Bash(bash *)` permissions, so it can:
- Build tools: `cargo build --release --manifest-path tools/rust/Cargo.toml`
- Run tools via wrapper: `bash tools/my-tool --help`

These permissions are configured in both the orchestrator workflow settings (CI) and locally.
