# zeri - Project Context

## Overview

Zeri is a CLI tool for generating AI assistant context files. Built with Laravel Zero, it creates structured documentation for Claude, Gemini, Codex, and Cursor AI assistants with advanced workflow management and specification creation capabilities.

**Key Features:**

-   Initialize project structure with `.zeri/` directory
-   Generate AI-specific context files from templates with mandatory workflow instructions
-   Support for multiple AI platforms (Claude, Gemini, Codex, Cursor) with platform-specific instructions
-   Specification creation workflow with `zeri add-spec` command
-   Self-updating capabilities
-   PHAR distribution for easy installation
-   Separation of AI instructions from source documentation (v1.6.0)

## Tech Stack

-   PHP 8.2+
-   Laravel Zero (micro-framework for console applications)
-   Box (PHAR building)
-   Pest (testing framework)
-   Composer (dependency management)

## Architecture

**Core Components:**

-   `app/Commands/` - CLI command implementations
-   `app/Generators/` - AI file generators (Claude, Gemini, Codex, Cursor)
-   `stubs/` - Template files for generation
-   `.zeri/` - User project structure for context files

**Key Files:**

-   `box.json` - PHAR build configuration
-   `config/app.php` - Application configuration and version
-   `config/self-update.php` - Self-update configuration

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

-   `getOutputFileName()`: Target file path
-   `generate()`: Main generation logic
-   `getGeneratedFiles()`: List of all files created (for multi-file generators)

## Current Focus

This project generates AI context files with enhanced workflow management. When making changes:

-   Consider how it affects all supported AI platforms (Claude, Gemini, Codex, Cursor)
-   Ensure stub templates remain flexible for different project types
-   Maintain clean separation between core application and generated content
-   Preserve the separation of AI instructions from source documentation (v1.6.0 architecture)
-   Ensure mandatory workflow instructions are properly enforced across all AI platforms

## Environment Setup

-   PHP 8.2 or higher
-   Composer
-   Git

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

1. **Reference @DEVELOPMENT.md** for detailed setup, architecture, and contribution guidelines
2. **Use development commands** (`php application`) rather than built binary during development
3. **Follow Laravel Zero patterns** - this is a console application framework
4. **Test thoroughly** - run tests and build before suggesting changes
5. **Maintain backward compatibility** - this tool is distributed as PHAR
6. **Update stub files** when adding new generators or templates
