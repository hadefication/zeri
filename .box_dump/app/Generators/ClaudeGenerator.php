<?php

namespace App\Generators;

class ClaudeGenerator extends BaseGenerator
{
public function getOutputFileName(): string
{
return 'CLAUDE.md';
}

public function generate(bool $force = false): bool
{
if (!$this->shouldRegenerate($force)) {
return false; 
}

$content = $this->buildClaudeContent();
return $this->writeOutput($content);
}

private function buildClaudeContent(): string
{
$content = [];


$content[] = "# Development Context for Claude";
$content[] = "";
$content[] = "This file provides comprehensive context for Claude to assist with development tasks.";
$content[] = "Generated on: " . date('Y-m-d H:i:s');
$content[] = "";


$context = $this->readFile('context.md');
if ($context) {
$content[] = "## Project Context";
$content[] = "";
$content[] = $context;
$content[] = "";
}


$standards = $this->readFile('standards.md');
if ($standards) {
$content[] = "## Code Standards & Best Practices";
$content[] = "";
$content[] = $standards;
$content[] = "";
}


$content[] = "## Development Workflows";
$content[] = "";

$workflows = ['coding.md', 'planning.md', 'debugging.md'];
foreach ($workflows as $workflow) {
$workflowContent = $this->readFile('workflows/' . $workflow);
if ($workflowContent) {
$title = ucfirst(str_replace('.md', '', $workflow));
$content[] = "### {$title} Workflow";
$content[] = "";
$content[] = $workflowContent;
$content[] = "";
}
}


$content[] = "## Project Documentation";
$content[] = "";

$projectDocs = ['roadmap.md', 'decisions.md', 'patterns.md'];
foreach ($projectDocs as $doc) {
$docContent = $this->readFile('project/' . $doc);
if ($docContent) {
$title = ucfirst(str_replace('.md', '', $doc));
$content[] = "### {$title}";
$content[] = "";
$content[] = $docContent;
$content[] = "";
}
}


$specs = $this->getSpecifications();
if (!empty($specs)) {
$content[] = "## Current Feature Specifications";
$content[] = "";
$content[] = "The following specifications represent current or upcoming work:";
$content[] = "";

foreach ($specs as $spec) {
$content[] = "### " . ucfirst(str_replace('-', ' ', $spec['name']));
$content[] = "";
$content[] = $spec['content'];
$content[] = "";
}
}


$content[] = "## Instructions for Claude";
$content[] = "";
$content[] = "When working on this project:";
$content[] = "";
$content[] = "1. **Context Awareness**: Consider all the above context when making recommendations";
$content[] = "2. **Standards Compliance**: Follow the established code standards and patterns";
$content[] = "3. **Workflow Integration**: Respect the development workflows described above";
$content[] = "4. **Specification Alignment**: Ensure any code changes align with current specifications";
$content[] = "5. **Documentation**: Update relevant documentation when making significant changes";
$content[] = "6. **Testing**: Follow the testing requirements and patterns established in the project";
$content[] = "";
$content[] = "Remember to ask clarifying questions if the requirements are unclear or if you need additional context about specific aspects of the project.";
$content[] = "";

return implode("\n", $content);
}
}