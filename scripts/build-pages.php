<?php

/**
 * Build static HTML pages from generate-*.php scripts.
 *
 * Each page contains a <script type="application/ld+json"> tag with the raw output
 * and a prettified JSON display. An index page links to all types.
 *
 * Usage: php scripts/build-pages.php
 * Output: _site/
 */

$projectRoot = dirname(__DIR__);
$outputDir = $projectRoot . '/_site';
$srcDir = $projectRoot . '/src';

// Clean and create output directory
if (is_dir($outputDir)) {
	array_map('unlink', glob($outputDir . '/*.html'));
} else {
	mkdir($outputDir, 0755, true);
}

// Discover generate scripts
$scripts = glob($srcDir . '/generate-*.php');
sort($scripts);

if (empty($scripts)) {
	echo "No generate scripts found in src/\n";
	exit(0);
}

$pages = [];

foreach ($scripts as $script) {
	$filename = basename($script);
	$slug = preg_replace('/^generate-(.+)\.php$/', '$1', $filename);

	echo "Processing {$filename}... ";

	// Run the script and capture output
	$output = [];
	$exitCode = 0;
	exec('php ' . escapeshellarg($script) . ' 2>/dev/null', $output, $exitCode);
	$jsonLd = trim(implode("\n", $output));

	if ($exitCode !== 0 || $jsonLd === '') {
		echo "SKIPPED (exit code {$exitCode})\n";
		fprintf(STDERR, "Warning: {$filename} failed with exit code {$exitCode}\n");
		continue;
	}

	// Try to decode JSON to get the @type
	$decoded = json_decode($jsonLd, true);
	$typeName = ucfirst($slug);
	if ($decoded !== null && isset($decoded['@type'])) {
		$type = $decoded['@type'];
		$typeName = is_array($type) ? implode(' / ', $type) : $type;
	}

	// Pretty-print the JSON
	$prettyJson = $decoded !== null
		? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
		: $jsonLd;

	$pages[] = [
		'slug' => $slug,
		'type' => $typeName,
		'jsonLd' => $jsonLd,
		'prettyJson' => $prettyJson,
		'sourceFile' => $filename,
	];

	echo "OK ({$typeName})\n";
}

if (empty($pages)) {
	echo "No pages generated (all scripts failed)\n";
	exit(1);
}

// Generate per-type pages
foreach ($pages as $page) {
	$escapedJson = htmlspecialchars($page['prettyJson'], ENT_QUOTES, 'UTF-8');
	$escapedType = htmlspecialchars($page['type'], ENT_QUOTES, 'UTF-8');
	$escapedSource = htmlspecialchars($page['sourceFile'], ENT_QUOTES, 'UTF-8');
	$firstType = explode(' / ', $page['type'])[0];
	$schemaUrl = 'https://schema.org/' . urlencode($firstType);

	$html = <<<HTML
	<!DOCTYPE html>
	<html lang="en">
	<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{$escapedType} — Schema.org JSON-LD QC</title>
	<script type="application/ld+json">
	{$page['jsonLd']}
	</script>
	<style>
	body { font-family: system-ui, sans-serif; max-width: 48rem; margin: 2rem auto; padding: 0 1rem; background: #0d1117; color: #e6edf3; }
	pre { background: #161b22; padding: 1rem; overflow-x: auto; border-radius: 6px; border: 1px solid #30363d; }
	code { font-size: 0.9rem; color: #e6edf3; }
	a { color: #58a6ff; }
	a:hover { color: #79c0ff; }
	h1, h2 { color: #e6edf3; }
	h1 a { color: #58a6ff; }
	.meta { color: #8b949e; font-size: 0.85rem; margin-top: 2rem; }
	.meta code { color: #8b949e; }
	</style>
	</head>
	<body>
	<p><a href="index.html">&larr; All types</a></p>
	<h1><a href="{$schemaUrl}">{$escapedType}</a></h1>
	<h2>JSON-LD Output</h2>
	<pre><code>{$escapedJson}</code></pre>
	<div class="meta">
	<p>Source: <code>{$escapedSource}</code></p>
	<p>Schema: <a href="{$schemaUrl}">{$schemaUrl}</a></p>
	</div>
	</body>
	</html>
	HTML;

	// Remove leading tabs from heredoc indentation
	$html = preg_replace('/^\t/m', '', $html);
	file_put_contents($outputDir . '/' . $page['slug'] . '.html', $html);
}

// Generate index page
$timestamp = gmdate('Y-m-d H:i:s') . ' UTC';
$count = count($pages);
$typeLinks = '';
foreach ($pages as $page) {
	$escapedType = htmlspecialchars($page['type'], ENT_QUOTES, 'UTF-8');
	$typeLinks .= "<li><a href=\"{$page['slug']}.html\">{$escapedType}</a></li>\n";
}

$indexHtml = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Schema.org JSON-LD QC — Example Pages</title>
<style>
body { font-family: system-ui, sans-serif; max-width: 48rem; margin: 2rem auto; padding: 0 1rem; background: #0d1117; color: #e6edf3; }
a { color: #58a6ff; }
a:hover { color: #79c0ff; }
h1, h2 { color: #e6edf3; }
ul { line-height: 1.8; }
code { background: #161b22; padding: 0.15rem 0.35rem; border-radius: 4px; font-size: 0.9rem; }
.meta { color: #8b949e; font-size: 0.85rem; margin-top: 2rem; }
</style>
</head>
<body>
<h1>Schema.org JSON-LD QC</h1>
<p>Example JSON-LD output from <a href="https://github.com/EvaLok/schema-org-json-ld-qc">evabee/schema-org-json-ld-qc</a>, each page containing a valid <code>&lt;script type="application/ld+json"&gt;</code> tag.</p>
<h2>Types ({$count})</h2>
<ul>
{$typeLinks}</ul>
<div class="meta">
<p>Generated: {$timestamp}</p>
<p>Repository: <a href="https://github.com/EvaLok/schema-org-json-ld-qc">EvaLok/schema-org-json-ld-qc</a></p>
</div>
</body>
</html>
HTML;

file_put_contents($outputDir . '/index.html', $indexHtml);

echo "\nGenerated {$count} pages in _site/\n";
