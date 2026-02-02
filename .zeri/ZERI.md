# Project Context

This file provides context for AI assistants to help with development tasks.

---

## Instructions for AI Assistants

When working on this project:

1. **Follow the established patterns** described in the patterns section
2. **Adhere to coding standards** outlined in the standards section
3. **Consider architectural decisions** when proposing changes
4. **Follow the development workflow** for implementation steps
5. **Reference current priorities** to align with project goals
6. **Check active specifications** for feature-specific requirements
7. **ONLY implement or start coding when files in .zeri/specs are referenced** - Do not write code without specific feature specifications

### Specification Workflow

**Always use `zeri add-spec <name>` to create new feature specifications—never create these files manually.**

```bash
# Create a new specification
zeri add-spec "feature-name"

# This creates .zeri/specs/feature-name.md with the standard template
```

#### Intelligent Spec Creation Process

When a user requests to create a specification, you MUST follow this process:

1. **Assess Completeness**: Analyze the user's request to determine if it contains sufficient detail for a complete specification.

2. **Ask Targeted Follow-up Questions**: If the request is incomplete, ask clarifying questions ONLY about missing information.

3. **Iterative Dialogue**: Continue asking follow-up questions based on user answers until you have gathered sufficient information for a complete specification.

4. **Create Specification**: Once you have comprehensive information, run `zeri add-spec "feature-name"` and populate ALL sections with detailed content based on the gathered information.

5. **Plan Implementation**: Break requirements into actionable tasks in the TODO section.

6. **Update TODOs in Real Time**: Mark each TODO item as soon as its implementation step finishes using `- [x]`.

7. **Review Before Coding**: Confirm the specification is complete with the user and explicitly ask whether it is ready for implementation before writing code.

**Critical Reminders**
- Do not implement any specification unless the user explicitly instructs you to begin coding.
- When user's request is vague (e.g., "add a new feature"), you MUST ask clarifying questions before creating the spec.
- When user's request is detailed, you may proceed directly to creating the spec without unnecessary questions.
- Update TODOs in real time—mark each checkbox immediately after its implementation step finishes.

### Context & Usage Limits

- **Trigger**: When you estimate only ~10–20% of the conversation context/tokens remain, pause immediately before continuing any coding or planning steps.
- **Capture progress**: Summarize what has been completed into the active `.zeri/specs/<feature>.md` file (Overview, Implementation Notes, and TODO items as needed) so future turns inherit the state.
- **Sync TODOs**: Mark finished items with `- [x]`, add new follow-ups, and ensure the checklist reflects the latest plan before resuming.
- **Resume intentionally**: Only continue work after the summary is saved and, if necessary, a fresh thread or trimmed history is established to recover context. Always restart by rereading the spec and confirming instructions.

### Key Reminders

- Always write tests for new functionality
- Follow the established code review process
- Consider performance and security implications
- Update documentation when making architectural changes

---

## Project Overview

## Overview
Zeri is a CLI tool for generating AI assistant context files. Built with Laravel Zero, it creates structured documentation for Claude, Gemini, Cursor, and Codex AI assistants with advanced workflow management and specification creation capabilities.

**Key Features:**
- Initialize project structure with `.zeri/` directory
- Generate AI-specific context files from templates with mandatory workflow instructions
- Support for multiple AI platforms (Claude, Gemini, Cursor, Codex) with platform-specific instructions
- Specification creation workflow with `zeri add-spec` command
- Self-updating capabilities
- PHAR distribution for easy installation
- Separation of AI instructions from source documentation (v1.6.0)

## Tech Stack
- PHP 8.2+
- Laravel Zero (micro-framework for console applications)
- Box (PHAR building)
- Pest (testing framework)
- Composer (dependency management)

## Architecture

**Core Components:**
- `app/Commands/` - CLI command implementations
- `app/Generators/` - AI file generators (Claude, Gemini, Cursor)
- `stubs/` - Template files for generation
- `.zeri/` - User project structure for context files

**Key Files:**
- `box.json` - PHAR build configuration
- `config/app.php` - Application configuration and version
- `config/self-update.php` - Self-update configuration

## Key Components

### Command Flow
1. **InitCommand**: Creates `.zeri/` structure using stub files
2. **AddSpecCommand**: Creates specification files from templates
3. **GenerateCommand**: Processes `.zeri/` files through generators
4. **Generators**: Read source files and apply templates to create AI-specific outputs

### Stub System
The application uses a two-level template system:
1. **Stubs** (`stubs/`) - Core templates for initial file creation
2. **Templates** (`.zeri/templates/`) - User-customizable templates for specifications

### File Generation
Generators extend `BaseGenerator` and implement:
- `getOutputFileName()`: Target file path
- `generate()`: Main generation logic
- `getGeneratedFiles()`: List of all files created (for multi-file generators)

## Current Focus
This project generates AI context files with enhanced workflow management. When making changes:
- Consider how it affects all supported AI platforms (Claude, Gemini, Cursor)
- Ensure stub templates remain flexible for different project types
- Maintain clean separation between core application and generated content
- Preserve the separation of AI instructions from source documentation (v1.6.0 architecture)
- Ensure mandatory workflow instructions are properly enforced across all AI platforms

## Environment Setup
- PHP 8.2 or higher
- Composer
- Git

### Development Commands
```bash
# Development (use php application instead of zeri binary)
php application init              # Initialize .zeri structure
php application generate claude   # Generate CLAUDE.md from .zeri files
php application add-spec "name"   # Add new specification
php application test             # Run tests

# Building
./build.sh                       # Recommended build method
php application app:build        # Manual build

# Code quality
./vendor/bin/pint                # Format code
./vendor/bin/pest                # Run tests directly
```

## Important Notes

### Instructions for Claude
When working on this project:

1. **Reference** @DEVELOPMENT.md for detailed setup, architecture, and contribution guidelines
2. **Use development commands** (`php application`) rather than built binary during development
3. **Follow Laravel Zero patterns** - this is a console application framework
4. **Test thoroughly** - run tests and build before suggesting changes
5. **Maintain backward compatibility** - this tool is distributed as PHAR
6. **Update stub files** when adding new generators or templates

---

## Development Practices

## Code Standards & Quality

### Code Style
Follow PSR-12 coding standards with Laravel Pint for formatting:
```bash
./vendor/bin/pint
```

**⚠️ MANDATORY: Run `./vendor/bin/pint` after every PHP file modification**

### Naming Conventions
- CamelCase for classes (e.g., `InitCommand`, `ClaudeGenerator`)
- snake_case for variables and functions
- Kebab-case for command names (e.g., `add-spec`)

### File Organization
```
app/
├── Commands/           # CLI commands
│   ├── InitCommand.php
│   ├── GenerateCommand.php
│   ├── AddSpecCommand.php
│   └── SelfUpdateCommand.php
└── Generators/         # AI file generators
    ├── BaseGenerator.php
    ├── ClaudeGenerator.php
    ├── GeminiGenerator.php
    └── CursorGenerator.php
```

### Documentation Standards
- Use PHPDoc for all public methods
- Document command signatures and descriptions
- Maintain comprehensive README and DEVELOPMENT.md

### Security Guidelines
- Sanitize all file paths and user inputs
- Use prepared statements for any database operations
- Validate template placeholders and user input

### Performance Considerations
- Optimize file operations for large projects
- Cache compiled templates where appropriate
- Use efficient string replacement for template processing

---

## Architecture Decisions

### Decision Template
- **Date**: 
- **Decision**: 
- **Context**: 
- **Options Considered**: 
- **Chosen Option**: 
- **Rationale**: 
- **Consequences**: 

### Recent Decisions
- **Framework Selection**: Laravel Zero for CLI framework (Project inception)
- **Build System**: Box for PHAR creation (Project inception)
- **Template System**: Two-level template system (stubs + user templates) (Project inception)
- **AI Instruction Architecture**: Separate AI-specific instructions from source documentation (v1.6.0)

### Key Architecture Decisions

#### Framework Selection
- **Date**: Project inception
- **Decision**: Use Laravel Zero for CLI framework
- **Context**: Need robust CLI framework with built-in features
- **Options Considered**: Symfony Console, custom solution, Laravel Zero
- **Chosen Option**: Laravel Zero
- **Rationale**: Provides console features, dependency injection, testing framework, and PHAR building
- **Consequences**: Leverages Laravel ecosystem, established patterns

#### Build System
- **Date**: Project inception  
- **Decision**: Use Box for PHAR creation
- **Context**: Need distributable single-file executable
- **Rationale**: Box is the standard for PHP PHAR building with compression and optimization
- **Consequences**: Easy distribution, but requires proper configuration

#### Template System
- **Date**: Project inception
- **Decision**: Two-level template system (stubs + user templates)
- **Context**: Need flexibility for different project types while maintaining structure
- **Rationale**: Stubs for initial structure, templates for user customization
- **Consequences**: More complex but highly flexible system

#### AI Instruction Architecture
- **Date**: v1.6.0 release
- **Decision**: Separate AI-specific instructions from source documentation
- **Context**: AI assistants were editing source documentation inappropriately
- **Options Considered**: Keep instructions in development.md, move to generated files, hybrid approach
- **Chosen Option**: Move mandatory instructions to generated AI files
- **Rationale**: Prevents inappropriate editing while maintaining workflow enforcement
- **Consequences**: Clear separation of concerns, better AI workflow control

### Technology Choices
PHP 8.2+ with Laravel Zero - chosen for team expertise and project requirements

### Design Patterns
MVC pattern, Repository pattern for data access, Generator pattern for AI file creation

---

## Code Patterns

### Standard Patterns
- Generator pattern for AI file generators extending BaseGenerator
- Command pattern for CLI operations extending Laravel Zero Command
- Template processing with placeholder replacement using {{PLACEHOLDER}} format

### Component Patterns
- Generator pattern for AI file generators
- Command pattern for CLI operations
- Template processing with placeholder replacement
- Two-level template system (stubs + user templates)

### Data Handling Patterns
- File operations through Laravel's File facade
- Template content processing with string replacement
- PHAR building with Box configuration
- Specification creation through AddSpecCommand workflow

### Error Handling Patterns
- Laravel Zero's built-in error handling
- Return appropriate exit codes (0 for success, non-zero for errors)
- User-friendly error messages
- Graceful handling of missing files and directories

### Testing Patterns
- Feature tests for command functionality in `tests/Feature/`
- Unit tests for individual components in `tests/Unit/`
- Use Pest testing framework
- Arrange-Act-Assert pattern for test structure

### Configuration Patterns
- Box configuration for PHAR building
- Laravel Zero application configuration
- Template-based configuration for different AI platforms
- Environment-based settings and feature flags

### Pattern Examples
```php
// Command Pattern
class ExampleCommand extends Command
{
    protected $signature = 'example {name} {--option}';
    protected $description = 'Example command description';

    public function handle()
    {
        // Command logic
        return 0;
    }
}

// Generator Pattern
abstract class BaseGenerator
{
    abstract protected function getOutputFileName(): string;
    abstract public function generate(): bool;
    public function getGeneratedFiles(): array { return [$this->getOutputFileName()]; }
}

// Template Processing
$content = $this->createFromStub('template.stub', [
    'PROJECT_NAME' => $projectName,
    'DESCRIPTION' => $description
]);
```

---

## Development Workflows

### Development Process
1. Fork repository and create feature branch
2. Use `php application` commands during development (not built binary)
3. Write tests for new functionality
4. Run tests and code formatting
5. Build and test executable
6. Create pull request

### Before Starting Development
```bash
git checkout main
git pull origin main
git checkout -b feature/new-feature
composer install
```

### Implementation Steps
1. **Write tests first** - Create failing tests for new functionality
2. **Implement feature** - Write minimal code to pass tests
3. **Run tests** - `php application test` or `./vendor/bin/pest`
4. **Format code** - `./vendor/bin/pint` (REQUIRED for every PHP file update)
5. **Build and test** - `./build.sh && ./builds/zeri --version`
6. **Code review** - Create PR for review

### Testing Workflow
```bash
# Run all tests
php application test

# Run specific test file
./vendor/bin/pest tests/Feature/InitCommandTest.php

# Run with coverage
./vendor/bin/pest --coverage
```

### Testing Requirements
Write tests for all new functionality, including command functionality and generator behavior

### Code Review Process
Pull request review with at least one approval, focusing on functionality and architectural consistency

### Code Review Guidelines
- All code must be reviewed before merge
- Check for PSR-12 compliance (ensure `./vendor/bin/pint` was run)
- Verify tests pass and cover new functionality
- Ensure backward compatibility
- Review security implications
- Confirm all PHP files are properly formatted with Pint

### Deployment Steps
**Always update version BEFORE building:**
```bash
# 1. Update version in config/app.php
# 2. Build
./build.sh
# 3. Test
./builds/zeri --version
# 4. Commit version change
git add config/app.php
git commit -m "Bump version to vx.y.z"
```

### Troubleshooting Common Issues

**Build fails with missing stubs:**
- Ensure all stub files are included in `box.json` directories
- Check `stubs/` directory is being packaged correctly

**Self-update signature errors:**
- Development uses custom `SelfUpdateCommand` to avoid signing issues
- Production would need proper PHAR signing setup

**Permission issues:**
```bash
chmod +x builds/zeri
sudo chown $(whoami) /usr/local/bin/zeri
```

---

## Feature Planning

### Planning Process
Requirements gathering, technical design, estimation with focus on CLI tool usability

### Requirements Gathering
Stakeholder interviews, user stories, acceptance criteria focused on developer workflow needs

### Technical Analysis
Architecture review, dependency analysis, risk assessment for CLI tool distribution

### Design Considerations
User experience for CLI tools, performance for file operations, security for template processing, maintainability

### Implementation Planning
Break down into tasks, estimate effort, plan sprints with focus on PHAR building requirements

### Risk Assessment
Identify technical risks including PHAR distribution challenges, template security, cross-platform compatibility

### Timeline Estimation
Story points, velocity tracking, buffer for unknowns especially around CLI tool complexity

---

## Adding New Features

### Adding a New Command
1. Create command class in `app/Commands/`
2. Extend `LaravelZero\Framework\Commands\Command`
3. Define `$signature` and `$description`
4. Implement `handle()` method
5. Add tests in `tests/Feature/`

### Adding a New AI Generator
1. Create generator class in `app/Generators/`
2. Extend `BaseGenerator`
3. Implement required methods
4. Create corresponding stub file in `stubs/`
5. Update `GenerateCommand::getGenerators()`
6. Add tests

### Adding New Stub Templates
1. Create `.stub` file in `stubs/`
2. Use `{{PLACEHOLDER}}` format for replacements
3. Update generator to use `createFromStub()` method
4. Handle newline conversion appropriately

---

## Debugging & Maintenance

### Debugging Process
1. **Reproduce issue** - Create minimal reproduction case
2. **Enable verbose output** - Use `-vvv` flag for detailed logging
3. **Check generated files** - Verify output matches expectations
4. **Isolate problem** - Test individual components
5. **Fix and verify** - Write test, fix issue, confirm resolution

### Common Issues

**Build fails with missing stubs:**
- Ensure all stub files are included in `box.json` directories
- Check `stubs/` directory is being packaged correctly

**Self-update signature errors:**
- Development uses custom `SelfUpdateCommand` to avoid signing issues
- Production would need proper PHAR signing setup

**Permission issues:**
```bash
chmod +x builds/zeri
sudo chown $(whoami) /usr/local/bin/zeri
```

### Debugging Tools
- Verbose output: `php application generate claude -vvv`
- Check generated files in current directory
- Use `var_dump()` or `dd()` for debugging during development
- Laravel Zero's built-in logging

### Log Analysis
Check application logs, error logs, system logs for CLI tool specific issues

### Performance Debugging
- Profile file operations for large projects
- Monitor memory usage during template processing
- Optimize string operations for large files
- Consider PHAR size optimization

### Error Tracking
Use error tracking service, categorize errors, prioritize fixes with focus on CLI tool reliability

### Resolution Documentation
- Document fixes in commit messages
- Update DEVELOPMENT.md for recurring issues
- Share solutions in GitHub issues/discussions

---

## Specification Implementation

### Creating Specifications

Use `zeri add-spec <name>` to create new feature specifications:

```bash
# Create a new specification
zeri add-spec "feature-name"

# This creates .zeri/specs/feature-name.md with the standard template
```

**Specification Structure:**
- **Overview**: Brief description of the feature or enhancement
- **Requirements**: Detailed list of functional requirements
- **Implementation Notes**: Technical considerations and dependencies
- **TODO**: Checklist for tracking implementation progress

### Specification Workflow

1. **Create Specification**: Use `zeri add-spec` command to create structured requirements
2. **Plan Implementation**: Break down requirements into actionable tasks
3. **Implement Features**: Follow the TODO checklist step by step
4. **Mark Progress**: Update TODOs in real-time during development
5. **Review and Complete**: Ensure all requirements are met

### Best Practices

**Specification Content:**
- Write clear, actionable requirements
- Include technical considerations and dependencies
- Reference existing patterns and conventions
- Consider testing and documentation needs

**Implementation Process:**
- Always start with a specification for non-trivial features
- Break complex features into smaller, manageable tasks
- Follow established coding patterns and conventions
- Write tests alongside implementation

### TODO Marking

Mark TODO items as complete when implementing specifications:

- Mark checkboxes as `- [x]` when completing each implementation step
- This helps track progress and manage development workflow
- Update TODOs in real-time during implementation

**Example:**
```markdown
## TODO
- [x] Design and plan implementation
- [x] Implement core functionality
- [ ] Add tests
- [ ] Update documentation
- [ ] Review and refine
- [ ] Mark specification as complete
```

### Specification Directory Structure

```
.zeri/
├── specs/                    # Feature specifications
│   ├── feature-name.md      # Individual specification files
│   └── another-feature.md   # Each spec is self-contained
└── templates/
    └── spec.md              # Template for new specifications
```

---

## Active Specifications

*(Specification references will be listed here)*


---

*Edit this file to update project context. Run `zeri generate <ai>` to update AI file references.*
