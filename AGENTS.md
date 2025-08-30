# Development Context for OpenAI Codex

This file provides structured context for OpenAI Codex CLI to assist with development tasks.
Generated on: 2025-08-30 01:07:31

## Project Overview

This document serves as memory and project documentation for Codex CLI's "Memory with AGENTS.md" functionality.

## Referenced Files

Please read the following project files for complete context:

**Core Configuration:**
- [@.zeri/project.md](.zeri/project.md) - Project overview, tech stack, and architecture
- [@.zeri/development.md](.zeri/development.md) - Standards, decisions, patterns, and development workflows


**Active Specifications:**
- [@.zeri/specs/add-codex-openai-support.md](.zeri/specs/add-codex-openai-support.md) - add-codex-openai-support specification
- [@.zeri/specs/add-yes-flag-to-init-command.md](.zeri/specs/add-yes-flag-to-init-command.md) - add-yes-flag-to-init-command specification
- [@.zeri/specs/development-md-spec-instructions.md](.zeri/specs/development-md-spec-instructions.md) - development-md-spec-instructions specification
- [@.zeri/specs/fix-gemini-import-warnings.md](.zeri/specs/fix-gemini-import-warnings.md) - fix-gemini-import-warnings specification
- [@.zeri/specs/improve-add-spec-generated-file.md](.zeri/specs/improve-add-spec-generated-file.md) - improve-add-spec-generated-file specification


---

## Development Instructions for Codex

When working on this project:

1. **Follow established patterns** described in the development practices
2. **Adhere to coding standards** outlined in the standards section
3. **Consider architectural decisions** when proposing changes
4. **Follow the development workflow** for implementation steps
5. **Reference active specifications** for feature-specific requirements
6. **ONLY implement when specifications are referenced** - Do not write code without specific feature specifications

## Code Development Rules

### Specification Workflow
When creating new features:

1. **Create Specification**: Use `zeri add-spec "feature-name"` command
2. **Plan Implementation**: Break down requirements into actionable tasks
3. **Implement Features**: Follow the TODO checklist step by step
4. **Mark Progress**: Update TODOs as `- [x]` when completing steps
5. **Review and Complete**: Ensure all requirements are met

### Implementation Process
- Always use `zeri add-spec` command for new feature specifications
- Start with a specification for non-trivial features
- Break complex features into smaller, manageable tasks
- Follow established coding patterns and conventions
- Write tests alongside implementation
- Update TODOs in real-time during development

## Code Quality Standards

### Required Actions
- Run `./vendor/bin/pint` after every PHP file modification
- Write tests for all new functionality
- Follow PSR-12 coding standards
- Use established naming conventions (CamelCase for classes, snake_case for variables)
- Maintain backward compatibility

### Testing Requirements
- Feature tests in `tests/Feature/`
- Unit tests in `tests/Unit/`
- Use Pest testing framework
- Run tests with `php application test`

## Build and Development Commands

### Development Commands
```bash
# Use during development (not built binary)
php application init              # Initialize .zeri structure
php application generate codex    # Generate AGENTS.md from .zeri files
php application add-spec "name"   # Add new specification
php application test             # Run tests

# Code quality
./vendor/bin/pint                # Format code (MANDATORY)
./vendor/bin/pest                # Run tests
```

### Build Commands
```bash
./build.sh                       # Recommended build method
php application app:build        # Manual build
```

## Key Development Notes

- Primarily edit files in `.zeri/` directory
- Never remove zeri file references from generated AI files
- Generated AI files can be edited but preserve all .zeri/ references
- Consider performance and security implications
- Update documentation when making architectural changes
- Use Laravel Zero patterns for console applications

---
*This context file is automatically generated from your .zeri configuration for OpenAI Codex CLI compatibility. Update the source files in .zeri/ to modify this content.*