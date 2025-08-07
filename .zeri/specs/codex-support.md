# Feature Specification: codex-support

## Overview

Implement comprehensive support for Codex AI integration within the zeri CLI tool. This includes creating a CodexGenerator that produces CODEX.md files with proper instructions and context for Codex AI assistant, following the established pattern of other AI generators in the system.

## Requirements

### Core Functionality

-   Create a CodexGenerator class extending BaseGenerator
-   Generate CODEX.md files with comprehensive Codex AI instructions
-   Include setup instructions for Codex integration
-   Provide usage examples and best practices for Codex
-   Reference project files and development workflow
-   Include troubleshooting and common issues section
-   Support force regeneration and backup options

### User Interface Requirements

-   Support CLI command: `zeri generate codex`
-   Include 'codex' in the valid AI types list
-   Maintain consistent output formatting with other generators
-   Provide clear success/error messages
-   Support --force, --backup, and --interactive flags

### Integration Requirements

-   Extend the existing BaseGenerator abstract class
-   Follow the established generator pattern (ClaudeGenerator, GeminiGenerator)
-   Integrate with the existing GenerateCommand system
-   Maintain compatibility with current project structure
-   Support 'all' generation mode to include Codex

### Performance and Security Requirements

-   Ensure fast generation time (< 1 second)
-   No external API calls during generation
-   Secure handling of any configuration data
-   Follow existing security patterns
-   Proper error handling and validation

## Implementation Notes

### Technical Considerations

-   Create `app/Generators/CodexGenerator.php` extending `BaseGenerator`
-   Add CodexGenerator import to `app/Commands/GenerateCommand.php`
-   Add 'codex' to the `$validAIs` array in GenerateCommand
-   Create `stubs/CODEX.md.stub` template file
-   Follow existing naming conventions and patterns

### Files to Modify

-   `app/Generators/CodexGenerator.php` (new file)
-   `stubs/CODEX.md.stub` (new file)
-   `app/Commands/GenerateCommand.php` (add codex import and option)
-   Update command help documentation

### Integration Points

-   Extend BaseGenerator abstract class
-   Implement required abstract methods (generate, getOutputFileName)
-   Follow the same interface as other generators
-   Integrate with existing command routing
-   Support all existing command flags and options

### Architectural Decisions

-   Use the same template-based approach as other generators
-   Maintain consistency with existing code structure
-   Follow Laravel Zero patterns and conventions
-   Use dependency injection where appropriate
-   Reuse existing BaseGenerator functionality

### Testing Strategy

-   Unit tests for CodexGenerator class
-   Integration tests for generate command with codex option
-   Feature tests for complete workflow
-   Template validation tests
-   Command help and validation tests

## TODO

-   [x] Create CodexGenerator class extending BaseGenerator
-   [x] Implement required abstract methods (generate, getOutputFileName)
-   [x] Create CODEX.md.stub template with comprehensive content
-   [x] Add CodexGenerator import to GenerateCommand
-   [x] Add 'codex' to validAIs array in GenerateCommand
-   [x] Add codex case to getGenerators method
-   [x] Test codex generation command
-   [x] Test codex with --force flag
-   [x] Test codex with --backup flag
-   [x] Test codex in 'all' generation mode
-   [x] Write unit tests for CodexGenerator
-   [x] Write integration tests for generate command
-   [x] Test complete generation workflow
-   [x] Update documentation to include codex generator
-   [x] Review and refine implementation
-   [x] Mark specification as complete
