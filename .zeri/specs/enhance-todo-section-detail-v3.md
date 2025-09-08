# Feature Specification: enhance-todo-section-detail-v3

## Overview
Enhance the TODO section in specification files created by AI assistants to be more elaborate and detailed. When AI creates specifications, the TODO section should contain detailed, actionable steps that reflect the actual implementation requirements. Manual `zeri add-spec` usage keeps the simpler default format for user flexibility.

## Requirements
- AI assistants should create specifications with detailed, actionable TODO items
- TODO items should reflect specific implementation steps needed for each feature
- Include technical tasks like file modifications, command updates, testing strategies  
- Maintain checkbox format for progress tracking by AI assistants
- Manual `zeri add-spec` command retains simple default TODO format for user flexibility
- Preserve backward compatibility with existing specifications

## Implementation Notes
- AI TODO guidelines added to generated AI files (CLAUDE.md, GEMINI.md, cursor-zeri.mdc, AGENTS.md)
- These files are stable and specifically designed for AI assistant instructions
- Keep the default `AddSpecCommand` simple for manual user flexibility
- AI assistants will read the guidelines from their respective context files
- Guidelines include detailed TODO format examples and best practices
- Maintain the established checkbox format: `- [ ] Task description`

## TODO
- [x] Analyze existing specifications to identify common implementation patterns
- [x] Design a system for generating detailed, spec-specific TODO items
- [x] Keep AddSpecCommand simple for manual usage flexibility
- [x] Add AI TODO guidelines to Claude generator (claude.md.stub)
- [x] Add AI TODO guidelines to Gemini generator (gemini.md.stub) 
- [x] Add AI TODO guidelines to Cursor generator (cursor-zeri.mdc.stub)
- [x] Add AI TODO guidelines to Codex generator (agents.md.stub)
- [x] Ensure TODO items are clear, actionable, and measurable
- [x] Verify compatibility with AI assistant workflow expectations
- [x] Validate the enhanced TODO format works for different spec types
- [x] Mark specification as complete