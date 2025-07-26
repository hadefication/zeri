<?php

namespace App\Generators;

class GeminiGenerator extends BaseGenerator
{
public function getOutputFileName(): string
{
return 'GEMINI.md';
}

public function generate(bool $force = false): bool
{
if (!$this->shouldRegenerate($force)) {
return false; 
}

$content = $this->buildGeminiContent();
return $this->writeOutput($content);
}

private function buildGeminiContent(): string
{
$content = [];


$content[] = "# GEMINI DEVELOPMENT ASSISTANT";
$content[] = "";
$content[] = "**ROLE**: Senior Software Developer Assistant";
$content[] = "**GENERATED**: " . date('Y-m-d H:i:s');
$content[] = "";


$content[] = "## PRIMARY RULES";
$content[] = "";
$content[] = "1. ALWAYS follow established code standards and patterns";
$content[] = "2. PRIORITIZE security and performance in all recommendations";
$content[] = "3. MAINTAIN consistency with existing codebase architecture";
$content[] = "4. REQUIRE comprehensive testing for all new features";
$content[] = "5. UPDATE documentation when making significant changes";
$content[] = "";


$context = $this->readFile('context.md');
if ($context) {
$content[] = "## PROJECT CONTEXT";
$content[] = "";
$content[] = $this->formatForGemini($context);
$content[] = "";
}


$standards = $this->readFile('standards.md');
if ($standards) {
$content[] = "## TECHNICAL STANDARDS";
$content[] = "";
$content[] = $this->formatForGemini($standards);
$content[] = "";
}


$content[] = "## DEVELOPMENT PROTOCOLS";
$content[] = "";

$workflows = ['coding.md', 'planning.md', 'debugging.md'];
foreach ($workflows as $workflow) {
$workflowContent = $this->readFile('workflows/' . $workflow);
if ($workflowContent) {
$title = strtoupper(str_replace('.md', '', $workflow));
$content[] = "### {$title} PROTOCOL";
$content[] = "";
$content[] = $this->formatForGemini($workflowContent);
$content[] = "";
}
}


$patterns = $this->readFile('project/patterns.md');
if ($patterns) {
$content[] = "## ARCHITECTURE GUIDELINES";
$content[] = "";
$content[] = $this->formatForGemini($patterns);
$content[] = "";
}


$roadmap = $this->readFile('project/roadmap.md');
if ($roadmap) {
$content[] = "## CURRENT OBJECTIVES";
$content[] = "";
$content[] = $this->formatForGemini($roadmap);
$content[] = "";
}


$specs = $this->getSpecifications();
if (!empty($specs)) {
$content[] = "## ACTIVE SPECIFICATIONS";
$content[] = "";

foreach ($specs as $spec) {
$specName = strtoupper(str_replace('-', ' ', $spec['name']));
$content[] = "### {$specName}";
$content[] = "";
$content[] = $this->formatForGemini($spec['content']);
$content[] = "";
}
}


$content[] = "## CONSTRAINTS";
$content[] = "";
$content[] = "1. ALL code must pass existing tests";
$content[] = "2. NO breaking changes without explicit approval";
$content[] = "3. SECURITY vulnerabilities are blocking issues";
$content[] = "4. PERFORMANCE regressions require immediate attention";
$content[] = "5. CODE reviews are mandatory before deployment";
$content[] = "";


$content[] = "## ACTION DIRECTIVES";
$content[] = "";
$content[] = "WHEN coding:";
$content[] = "- Write clean, readable, maintainable code";
$content[] = "- Include comprehensive error handling";
$content[] = "- Add appropriate logging and monitoring";
$content[] = "- Follow established naming conventions";
$content[] = "";
$content[] = "WHEN debugging:";
$content[] = "- Reproduce the issue first";
$content[] = "- Identify root cause before fixing symptoms";
$content[] = "- Write tests to prevent regression";
$content[] = "- Document the solution";
$content[] = "";
$content[] = "WHEN planning:";
$content[] = "- Consider all stakeholder requirements";
$content[] = "- Assess technical risks and dependencies";
$content[] = "- Plan for testing and deployment";
$content[] = "- Estimate effort realistically";
$content[] = "";

return implode("\n", $content);
}

private function formatForGemini(string $content): string
{

$lines = explode("\n", $content);
$formatted = [];

foreach ($lines as $line) {
$line = trim($line);
if (empty($line)) {
$formatted[] = "";
continue;
}


if (strpos($line, '#') === 0) {
$level = substr_count($line, '#');
$text = trim(str_replace('#', '', $line));
$formatted[] = str_repeat('#', $level) . ' ' . strtoupper($text);
} else {
$formatted[] = $line;
}
}

return implode("\n", $formatted);
}
}