# Development Context for Claude

This file provides context for Claude to assist with Zeri development tasks.

## Core Documentation

**Primary Development Guide:** See [@DEVELOPMENT.md](DEVELOPMENT.md) for comprehensive setup, build, and contribution instructions.

## Project Overview

Zeri is a CLI tool for generating AI assistant context files. Built with Laravel Zero, it creates structured documentation for Claude, Gemini, and Cursor AI assistants.

**Key Features:**
- Initialize project structure with `.zeri/` directory
- Generate AI-specific context files from templates
- Support for multiple AI platforms (Claude, Gemini, Cursor)
- Self-updating capabilities
- PHAR distribution for easy installation

## Quick Development Commands

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

## Architecture Notes

**Core Components:**
- `app/Commands/` - CLI command implementations
- `app/Generators/` - AI file generators (Claude, Gemini, Cursor)
- `stubs/` - Template files for generation
- `.zeri/` - User project structure for context files

**Key Files:**
- `box.json` - PHAR build configuration
- `config/app.php` - Application configuration and version
- `config/self-update.php` - Self-update configuration

## Development Workflow

1. Use `php application` commands during development
2. Follow PSR-12 coding standards
3. Add tests for new functionality
4. Build and test with `./build.sh`
5. Format code with Pint before committing

## Instructions for Claude

When working on this project:

1. **Reference @DEVELOPMENT.md** for detailed setup, architecture, and contribution guidelines
2. **Use development commands** (`php application`) rather than built binary during development
3. **Follow Laravel Zero patterns** - this is a console application framework
4. **Test thoroughly** - run tests and build before suggesting changes
5. **Maintain backward compatibility** - this tool is distributed as PHAR
6. **Update stub files** when adding new generators or templates

## Current Focus

This project generates AI context files. When making changes:
- Consider how it affects all supported AI platforms (Claude, Gemini, Cursor)
- Ensure stub templates remain flexible for different project types
- Maintain clean separation between core application and generated content

---

*For complete development details, build instructions, troubleshooting, and contribution guidelines, see [DEVELOPMENT.md](DEVELOPMENT.md).*