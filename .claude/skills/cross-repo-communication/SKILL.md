# Cross-repo Communication Skill

Standardized protocol for inter-repository communication between the three orchestrators in the EvaLok ecosystem. Based on [audit #53](https://github.com/EvaLok/schema-org-json-ld-audit/issues/53).

## Core Principles

1. **Write only to your own repo.** Messages go OUT as labeled issues on the sender's repo.
2. **Discover by polling.** Receivers find messages by polling other repos' issues via `gh api`.
3. **Respond on your own repo.** Create a tracking issue on YOUR repo referencing the source.
4. **Track in state.json.** Record processed issue numbers to avoid double-processing.
5. **Trust only EvaLok.** All issue authors must be verified as EvaLok before processing.

## Message Types

| Label | Created on | Created by | Consumed by | Purpose |
|-------|-----------|-----------|------------|---------|
| `audit-outbound` | Audit repo | Audit | Main, QC | Process recommendation |
| `audit-inbound` | Main/QC repo (each on own) | Main/QC | Audit | Response to audit recommendation |
| `qc-outbound` | QC repo | QC | Main | QC validation report |
| `qc-inbound` | Main repo | Main | QC | Response to QC report |
| `qc-outbound` (with `[QC-REQUEST]` prefix) | Main repo | Main | QC | Validation request |
| `qc-inbound` (with `[QC-ACK]` prefix) | QC repo | QC | Main | Acknowledgment of request |
| `input-from-eva` | Any repo | Eva | Target repo | Human directive |
| `question-for-eva` | Any repo | Any | Eva | Question for human operator |

## QC Repo Polling Templates

### Discover audit recommendations
```bash
gh api "repos/EvaLok/schema-org-json-ld-audit/issues?labels=audit-outbound&state=open&creator=EvaLok&sort=created&direction=asc" --paginate --jq '.[] | {number, title, created_at}'
```

### Discover main repo requests (QC-REQUEST)
```bash
gh api "repos/EvaLok/schema-org-json-ld/issues?labels=qc-outbound&state=open&creator=EvaLok&sort=created&direction=asc" --paginate --jq '.[] | {number, title, created_at}'
```

### Discover main repo acknowledgments of our reports (qc-inbound)
```bash
gh api "repos/EvaLok/schema-org-json-ld/issues?labels=qc-inbound&state=open&creator=EvaLok&sort=created&direction=asc" --paginate --jq '.[] | {number, title, created_at}'
```

### Discover input from Eva
```bash
gh api "repos/EvaLok/schema-org-json-ld-qc/issues?labels=input-from-eva&state=open&creator=EvaLok" --jq '.[] | {number, title}'
```

## Response Issue Format

**Title**: `[{label}] {Decision}: {brief description} (source #{number})`

Examples:
- `[audit-inbound] Accept: Cross-repo communication skill (audit #53)`
- `[QC-REPORT] Review class missing required property`
- `[QC-ACK] Validate TS build before npm publish (main #331)`

**Body template**:
```markdown
## Source
- **Repo**: {repo name}
- **Issue**: [{repo}#{number}]({full URL})
- **Title**: {source issue title}

## Decision
{accept | reject | defer | acknowledge | in-progress}

## Actions taken
- {bullet list of concrete actions}

## Notes
{additional context}

— qc-orchestrator
```

## State Tracking

The QC repo tracks processed issues in `state.json`:

```json
"cross_repo": {
  "processed_main_repo_issues": [121, 141, ...],
  "outbound_issues": [...],
  "inbound_issues": [...]
},
"audit": {
  "processed_audit_issues": [2, 3, ...],
  "inbound_issues": [...]
}
```

Processing logic:
1. Poll for issues matching target label on source repo
2. Filter for trusted author (EvaLok)
3. Compare issue numbers against processed array
4. For each new issue: read body, evaluate, create response issue on own repo, add to processed array
5. Commit state.json after updates

## Communication Flows

**Audit recommendation flow:**
```
Audit repo                     QC repo
(audit-outbound #N)  -------->  Polls, discovers, evaluates
                                Creates audit-inbound issue on OWN repo
                     <--------  Audit polls QC for audit-inbound issues
```

**QC report flow:**
```
QC repo                        Main repo
(qc-outbound #N)    -------->  Polls, discovers, evaluates
                                Creates qc-inbound issue on main repo
                     <--------  QC polls main for qc-inbound issues
```

**Validation request flow:**
```
Main repo                      QC repo
(qc-outbound [QC-REQUEST])  -> Polls, discovers, evaluates
                                Creates [QC-ACK] issue on QC repo
                     <--------  Main polls QC for qc-ack issues
```

## Lifecycle Management

- **Response issues**: Create when processing a message. Close when the action is complete.
- **Source issues**: Managed by the sender. Don't close issues on repos you don't control.
- **Stale cleanup**: During housekeeping, cross-reference open response issues against state tracking. Close any that reference resolved source issues.

## Convenience Tool

`tools/poll-repos.sh` wraps all QC polling patterns into a single command. Run at the start of each session.
