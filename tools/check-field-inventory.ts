#!/usr/bin/env bun
/**
 * Field inventory enforcement tool.
 * Compares all mutable leaf paths in state.json against field_inventory entries.
 * Reports gaps (fields without inventory entries).
 *
 * Usage: bun tools/check-field-inventory.ts [--fix]
 *
 * Per audit #87: replaces LLM-driven manual comparison with programmatic check.
 */

import { readFileSync } from "fs";
import { join } from "path";

const stateFile = join(import.meta.dir, "..", "state.json");
const state = JSON.parse(readFileSync(stateFile, "utf-8"));

// Paths that are structural/immutable/append-only and should be excluded
const excludedPrefixes = [
	"field_inventory",          // The inventory itself
	"self_improvement_actions", // Append-only log
	"schema_types.covered.",    // Individual type entries checked via wildcards
	"cross_repo.outbound_issues.", // Individual issue entries checked via wildcards
	"cross_repo.inbound_issues.",  // Individual issue entries checked via wildcards
	"agent_sessions.completed",    // Append-only history
	"copilot_metrics.failure_analysis.", // Individual failure entries
	"e2e_status.warning_baselines.types.", // Individual type baselines
	"ts_validation.parity_results.types_tested.", // Individual type results
	"ts_validation.vitest_results.", // Sub-fields of vitest results
	"session_history.",            // Individual session entries
];

// Paths that are truly immutable/structural
const immutablePaths = [
	"package.name",
	"package.version",
	"schema_types.enums",
];

// Recursively enumerate all leaf paths in an object
function enumerateLeafPaths(obj: any, prefix = ""): string[] {
	const paths: string[] = [];
	if (obj === null || obj === undefined || typeof obj !== "object") {
		return [prefix];
	}
	if (Array.isArray(obj)) {
		// The array itself is a mutable field
		paths.push(prefix);
		// Also recurse into array items (objects) to check per-item wildcard patterns
		for (let i = 0; i < obj.length; i++) {
			if (obj[i] && typeof obj[i] === "object" && !Array.isArray(obj[i])) {
				paths.push(...enumerateLeafPaths(obj[i], `${prefix}.${i}`));
			}
		}
		return paths;
	}
	for (const key of Object.keys(obj)) {
		const fullPath = prefix ? `${prefix}.${key}` : key;
		const child = obj[key];
		if (child === null || child === undefined || typeof child !== "object") {
			paths.push(fullPath);
		} else {
			// Recurse into both objects and arrays
			paths.push(...enumerateLeafPaths(child, fullPath));
		}
	}
	return paths;
}

// Normalize a concrete path to match wildcard patterns in the inventory
function normalizeToPattern(path: string): string[] {
	const patterns: string[] = [path];

	// Handle schema_types.covered.<Type>.<field> → schema_types.covered.*.<field>
	const coveredMatch = path.match(/^schema_types\.covered\.([^.]+)\.(.+)$/);
	if (coveredMatch) {
		patterns.push(`schema_types.covered.*.${coveredMatch[2]}`);
	}

	// Handle any array-of-objects pattern: parent.<index>.<field> → parent.*.<field>
	// This covers cross_repo.outbound_issues, cross_repo.inbound_issues, audit.inbound_issues, etc.
	const arrayItemMatch = path.match(/^(.+)\.(\d+)\.(.+)$/);
	if (arrayItemMatch) {
		patterns.push(`${arrayItemMatch[1]}.*.${arrayItemMatch[3]}`);
	}

	return patterns;
}

// Get the inventory entries
const inventory = state.field_inventory?.fields ?? {};
const inventoryKeys = new Set(Object.keys(inventory));

// Enumerate all leaf paths
const allPaths = enumerateLeafPaths(state);

// Filter to mutable paths (exclude structural/immutable)
const mutablePaths = allPaths.filter((path) => {
	// Exclude immutable paths
	if (immutablePaths.includes(path)) return false;
	// Exclude paths under excluded prefixes
	if (excludedPrefixes.some((prefix) => path.startsWith(prefix))) return false;
	// Exclude individual array item fields (e.g., cross_repo.outbound_issues.0.issue)
	// These are covered by wildcard inventory entries (*.status, *.note)
	// Only keep the normalized wildcard form
	if (/\.\d+\./.test(path)) return false;
	return true;
});

// Check each mutable path against inventory
const gaps: string[] = [];
const matched = new Set<string>();

for (const path of mutablePaths) {
	const patterns = normalizeToPattern(path);
	const found = patterns.some((p) => inventoryKeys.has(p));
	if (found) {
		for (const p of patterns) {
			if (inventoryKeys.has(p)) matched.add(p);
		}
	} else {
		gaps.push(path);
	}
}

// For wildcard inventory entries, check that the parent collection exists
// e.g., "schema_types.covered.*.last_tested" is valid if schema_types.covered has entries
for (const key of inventoryKeys) {
	if (key.includes("*") && !matched.has(key)) {
		// Check if the wildcard pattern's parent collection has entries
		const parentPath = key.split(".*")[0];
		const hasEntries = allPaths.some((p) => p.startsWith(parentPath + "."));
		if (hasEntries) matched.add(key);
	}
}

// Check for inventory entries that don't match any actual path (orphans)
const orphans = [...inventoryKeys].filter((k) => !matched.has(k));

// Report
if (gaps.length === 0 && orphans.length === 0) {
	console.log("✓ Field inventory is complete. All mutable fields have inventory entries.");
	process.exit(0);
} else {
	if (gaps.length > 0) {
		console.log(`✗ ${gaps.length} mutable field(s) missing from field_inventory:`);
		for (const gap of gaps.sort()) {
			console.log(`  - ${gap}`);
		}
	}
	if (orphans.length > 0) {
		console.log(`\n⚠ ${orphans.length} inventory entry(ies) with no matching field in state.json:`);
		for (const orphan of orphans.sort()) {
			console.log(`  - ${orphan}`);
		}
	}
	process.exit(1);
}
