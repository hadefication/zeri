<?php

namespace App\Generators;

class CursorGenerator extends BaseGenerator
{
public function getOutputFileName(): string
{
return '.cursor/rules';
}

public function generate(bool $force = false): bool
{
if (!$this->shouldRegenerate($force)) {
return false; 
}

$content = $this->buildCursorContent();
return $this->writeOutput($content);
}

private function buildCursorContent(): string
{
$rules = [];


$rules[] = "# Cursor Rules for Development";
$rules[] = "# Generated on: " . date('Y-m-d H:i:s');
$rules[] = "";


$context = $this->readFile('context.md');
$techStack = $this->extractTechStack($context);


$rules[] = "## Core Development Rules";
$rules[] = "";

if ($techStack) {
$rules[] = "- Use {$techStack} as primary technology stack";
}

$standards = $this->readFile('standards.md');
if ($standards) {
$codeStyleRules = $this->extractCodeStyleRules($standards);
foreach ($codeStyleRules as $rule) {
$rules[] = "- {$rule}";
}
}

$rules[] = "- Write comprehensive tests for all new features";
$rules[] = "- Follow security best practices";
$rules[] = "- Optimize for performance and maintainability";
$rules[] = "- Use meaningful variable and function names";
$rules[] = "- Include proper error handling";
$rules[] = "";


$patterns = $this->readFile('project/patterns.md');
if ($patterns) {
$orgRules = $this->extractOrganizationRules($patterns);
if (!empty($orgRules)) {
$rules[] = "## File Organization";
$rules[] = "";
foreach ($orgRules as $rule) {
$rules[] = "- {$rule}";
}
$rules[] = "";
}
}


$specs = $this->getSpecifications();
if (!empty($specs)) {
$rules[] = "## Current Work";
$rules[] = "";
$rules[] = "Active specifications:";
foreach ($specs as $spec) {
$summary = $this->extractSpecSummary($spec['content']);
$rules[] = "- {$spec['name']}: {$summary}";
}
$rules[] = "";
}


$rules[] = "## Quick Reference";
$rules[] = "";
$rules[] = "Development workflow:";
$rules[] = "1. Create feature branch";
$rules[] = "2. Write failing tests";
$rules[] = "3. Implement feature";
$rules[] = "4. Ensure tests pass";
$rules[] = "5. Code review";
$rules[] = "6. Merge to main";
$rules[] = "";


$rules[] = "Testing requirements:";
$rules[] = "- Unit tests for business logic";
$rules[] = "- Integration tests for API endpoints";
$rules[] = "- Feature tests for user workflows";
$rules[] = "";


$commonPatterns = $this->extractCommonPatterns($patterns);
if (!empty($commonPatterns)) {
$rules[] = "Common patterns:";
foreach ($commonPatterns as $pattern) {
$rules[] = "- {$pattern}";
}
$rules[] = "";
}

return implode("\n", $rules);
}

private function extractTechStack(string $content): string
{

if (preg_match('/Tech Stack[:\s]*(.+?)(?:\n|$)/i', $content, $matches)) {
return trim($matches[1]);
}
return '';
}

private function extractCodeStyleRules(string $content): array
{
$rules = [];


$lines = explode("\n", $content);
foreach ($lines as $line) {
$line = trim($line);
if (preg_match('/^[-*]\s*(.+)/', $line, $matches)) {
$rule = trim($matches[1]);
if (strlen($rule) > 10 && strlen($rule) < 100) {
$rules[] = $rule;
}
}
}


if (empty($rules)) {
$rules = [
"Follow PSR-12 coding standards",
"Use descriptive variable names",
"Keep functions small and focused",
"Comment complex logic"
];
}

return array_slice($rules, 0, 5); 
}

private function extractOrganizationRules(string $content): array
{
$rules = [];


if (strpos($content, 'organize') !== false || strpos($content, 'structure') !== false) {
$rules[] = "Organize files by feature/domain";
$rules[] = "Keep related files together";
$rules[] = "Use consistent naming conventions";
}

return $rules;
}

private function extractSpecSummary(string $content): string
{

if (preg_match('/Overview[:\s]*(.+?)(?:\n|$)/i', $content, $matches)) {
return trim($matches[1]);
}

if (preg_match('/Description[:\s]*(.+?)(?:\n|$)/i', $content, $matches)) {
return trim($matches[1]);
}


$lines = explode("\n", $content);
foreach ($lines as $line) {
$line = trim($line);
if (!empty($line) && !str_starts_with($line, '#') && strlen($line) > 10) {
return substr($line, 0, 80) . (strlen($line) > 80 ? '...' : '');
}
}

return 'New feature specification';
}

private function extractCommonPatterns(string $content): array
{
$patterns = [];


if (preg_match_all('/pattern[:\s]*(.+?)(?:\n|$)/i', $content, $matches)) {
foreach ($matches[1] as $pattern) {
$pattern = trim($pattern);
if (strlen($pattern) > 5 && strlen($pattern) < 80) {
$patterns[] = $pattern;
}
}
}

return array_slice($patterns, 0, 3); 
}
}