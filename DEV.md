# Development Guide

This guide covers development setup, building, testing, and contributing to Zeri.

## Framework

Zeri is built with [Laravel Zero](https://laravel-zero.com), a micro-framework for building console applications. Laravel Zero provides:

- Command structure and routing
- Service container and dependency injection  
- Configuration management
- Testing framework (Pest)
- Build system with Box for PHAR creation
- Self-update capabilities

Understanding Laravel Zero concepts will help when developing new features.

## Requirements

- PHP 8.2 or higher
- Composer
- Git

## Setup

### Clone and Install Dependencies

```bash
# Clone the repository
git clone https://github.com/hadefication/zeri.git
cd zeri

# Install dependencies
composer install

# Initialize Zeri structure for AI context (recommended)
php application init claude
```

### Development Commands

For development, use the `php application` command instead of the built `zeri` binary:

```bash
# Run commands during development
php application init
php application generate claude
php application generate codex
php application add-spec "test-feature"

# After global installation, use:
zeri init
zeri generate claude
zeri generate codex
zeri add-spec "test-feature"
```

## Building

### Recommended Method (Build Script)

```bash
./build.sh
```

The build script:
- Optimizes the autoloader for production
- Builds the PHAR with Box
- Restores development autoloader
- Tests the executable
- Shows build statistics

### Manual Build

```bash
php application app:build
```

The executable will be created at `builds/zeri`.

### Build Configuration

The build process is configured in `box.json`:

```json
{
    "main": "application",
    "output": "builds/zeri", 
    "directories": ["app", "bootstrap", "config", "stubs", "vendor"],
    "files": ["composer.json"],
    "compression": "GZ",
    "compactors": [
        "KevinGH\\Box\\Compactor\\Php",
        "KevinGH\\Box\\Compactor\\Json"
    ]
}
```

## Testing

### Run Tests

```bash
php application test

# Or using Pest directly
./vendor/bin/pest
```

### Test Structure

- `tests/Feature/` - Feature tests for command functionality
- `tests/Unit/` - Unit tests for individual components

## Project Structure

### Core Components

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
    ├── CodexGenerator.php
    ├── GeminiGenerator.php
    └── CursorGenerator.php
```

### Configuration

```
config/
├── app.php             # Application configuration
├── commands.php        # Available commands
└── self-update.php     # Self-update configuration
```

### Templates and Stubs

```
stubs/                  # Template files for generation
├── project.md.stub
├── development.md.stub
├── CLAUDE.md.stub
├── GEMINI.md.stub
├── cursor-zeri.mdc.stub
└── templates/
    └── spec.md.stub
```

## Architecture

### Command Flow

1. **InitCommand**: Creates `.zeri/` structure using stub files
2. **AddSpecCommand**: Creates specification files from templates
3. **GenerateCommand**: Processes `.zeri/` files through generators
4. **Generators**: Read source files and apply templates to create AI-specific outputs

Supported AI agents and their output files:
- **Claude**: `CLAUDE.md`
- **Gemini**: `GEMINI.md`
- **Cursor**: `.cursor/rules/zeri.mdc`
- **Codex (OpenAI Codex CLI)**: `AGENTS.md`

### Stub System

The application uses a two-level template system:

1. **Stubs** (`stubs/`) - Core templates for initial file creation
2. **Templates** (`.zeri/templates/`) - User-customizable templates for specifications

### File Generation

Generators extend `BaseGenerator` and implement:

- `getOutputFileName()`: Target file path
- `generate()`: Main generation logic
- `getGeneratedFiles()`: List of all files created (for multi-file generators)

## Adding New Features

**IMPORTANT**: Before implementing any new feature, you must create a specification using:

```bash
zeri add-spec "feature-name"
```

This creates a structured specification file in `.zeri/specs/` that should be filled out with requirements, technical design, and implementation details before coding begins.

### Supported AI Agents

Zeri currently supports generating context files for multiple AI agents:
- `claude` → generates `CLAUDE.md`
- `gemini` → generates `GEMINI.md`
- `cursor` → generates `.cursor/rules/zeri.mdc`
- `codex` → generates `AGENTS.md` for the OpenAI Codex CLI

Generate for a specific agent or all:
```bash
php application generate codex
php application generate all

# Or after install
zeri generate codex
zeri generate all
```

### Feature Development Workflow

1. **Create Specification**: `zeri add-spec "feature-name"`
2. **Fill out specification** in `.zeri/specs/feature-name.md` with:
   - Requirements and acceptance criteria
   - Technical design and architecture decisions
   - Implementation plan and considerations
3. **Review specification** with team/stakeholders if applicable
4. **Implement feature** following the specification
5. **Mark TODO items as complete** during implementation to track progress and manage AI usage limits
6. **Update specification** with any changes made during implementation
7. **AI context automatically updates** when .zeri files change

**Important**: Always mark TODO checkboxes (`- [x]`) as you complete each implementation step. This helps track progress and manage AI assistant usage limits effectively.

### Adding a New Command

1. **Create specification first**: `zeri add-spec "new-command"`
2. Create command class in `app/Commands/`
3. Extend `LaravelZero\Framework\Commands\Command`
4. Define `$signature` and `$description` properties
5. Implement `handle()` method
6. Add to `config/commands.php` if needed (auto-discovery usually handles this)

**Example:**
```php
<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class ExampleCommand extends Command
{
    protected $signature = 'example {name} {--option}';
    protected $description = 'Example command description';

    public function handle()
    {
        $name = $this->argument('name');
        $option = $this->option('option');
        
        $this->info("Hello {$name}!");
        
        return 0;
    }
}
```

### Adding a New AI Generator

1. **Create specification first**: `zeri add-spec "new-ai-generator"`
2. Create generator class in `app/Generators/`
3. Extend `BaseGenerator`
4. Implement required methods
5. Create corresponding stub file in `stubs/`
6. Update `GenerateCommand::getGenerators()`

### Adding New Stub Templates

1. Create `.stub` file in `stubs/`
2. Use `{{PLACEHOLDER}}` format for replacements
3. Update generator to use `createFromStub()` method
4. Handle newline conversion with `\n` in placeholders

### Specification Format

The `zeri add-spec` command generates lean, focused specification files with:

- **Overview**: Brief description of the feature
- **Requirements**: Essential functional requirements
- **Implementation Notes**: Technical considerations and dependencies
- **TODO**: Checkbox list for tracking implementation progress

The TODO section uses markdown checkboxes that can be marked as complete by AI assistants during implementation to track progress and manage usage limits effectively.

**Usage**: Mark items as complete (`- [x]`) as you implement each step. This provides clear progress tracking and helps manage AI assistant usage efficiently.

## Debugging

### Important: AI File Regeneration

**Understanding `--force` vs Automatic Updates:**

- **Normal behavior**: AI files (CLAUDE.md, GEMINI.md, etc.) automatically reflect changes when you modify `.zeri/` source files
- **Use `--force` only when**: You need to completely regenerate files from scratch (e.g., after template changes or corruption)
- **Don't use `--force`** as part of normal development workflow - it's not needed for reflecting .zeri changes

**Key Point**: Once AI files are generated and you start working with an AI assistant, those files serve as a stable interface. The AI assistant should reference the structured `.zeri/` files for the most current information.

### Enable Verbose Output

```bash
php application generate claude -vvv
```

### Check Generated Files

During development, generated files are created in the current directory or specified path. Check:

- `CLAUDE.md`
- `GEMINI.md` 
- `AGENTS.md` (Codex)
- `.cursor/rules/zeri.mdc`

### Common Issues

**Build fails with missing stubs:**
- Ensure all stub files are included in `box.json` directories
- Check `stubs/` directory is being packaged

**Self-update signature errors:**
- Development uses custom `SelfUpdateCommand` to avoid signing issues
- Laravel Zero's built-in updater requires PHAR signing
- Production would need proper PHAR signing setup

## Release Process

We use the `release.sh` script to automate releases end‑to‑end.

### Prerequisites

- On branch `main` and working tree clean (script will commit pending changes).
- Optional: GitHub CLI `gh` installed and authenticated to auto‑create releases.
- Make sure `release.sh` is executable: `chmod +x release.sh`.

### Standard Release (Recommended)

```bash
# Interactive mode (choose patch/minor/major/custom)
./release.sh

# Auto‑increment
./release.sh patch   # 1.7.0 → 1.7.1
./release.sh minor   # 1.7.0 → 1.8.0
./release.sh major   # 1.7.0 → 2.0.0

# Set an explicit version
./release.sh 1.8.5

# Preview actions without changing anything
./release.sh patch --dry-run
```

The script will:
- Run tests and code formatting.
- Update `config/app.php` version automatically.
- Build the PHAR via `./build.sh` (outputs to `builds/zeri`).
- Commit the version bump and create tag `vX.Y.Z`.
- Push `main` and the tag.
- Create a GitHub release and attach `builds/zeri` (skips if `gh` not available).

After completion, verify with:
```bash
./builds/zeri --version
```

### Manual Fallback (If Needed)

Only if you cannot use `release.sh`:
1) Update `config/app.php` version. 2) `./build.sh`. 3) Verify `./builds/zeri --version`. 4) Commit: `git add config/app.php && git commit -m "Bump version to vX.Y.Z"`. 5) Tag and push: `git tag vX.Y.Z && git push origin main && git push origin vX.Y.Z`. 6) Create GitHub release (attach `builds/zeri`).

## Contributing

### Code Style

This project uses Laravel Pint for code formatting:

```bash
./vendor/bin/pint
```

### Pull Request Process

1. Fork the repository
2. Create feature branch: `git checkout -b feature/new-feature`
3. **Create specification**: `zeri add-spec "new-feature"`
4. **Fill out specification** with requirements and design
5. Make changes and add tests following the specification
6. Run tests: `php application test`
7. Format code: `./vendor/bin/pint`
8. Build and test: `./build.sh && ./builds/zeri --version`
9. **AI context files automatically reflect changes to .zeri files**
10. Commit and push changes (including specification files)
11. Create pull request with reference to specification

### Guidelines

- **Always create specifications before implementing features** using `zeri add-spec`
- Follow PSR-12 coding standards
- Add tests for new functionality
- Update documentation for new features
- Keep commit messages clear and descriptive
- Ensure backward compatibility
- Include specification files in commits
- Reference specifications in pull requests

## Troubleshooting

### Permission Issues

```bash
# Fix executable permissions
chmod +x builds/zeri

# Fix install permissions
sudo chown $(whoami) /usr/local/bin/zeri
```

### PHP Version Issues

Check PHP version for CLI:

```bash
php -v
composer show php
```

### Autoloader Issues

Regenerate autoloader:

```bash
composer dump-autoload --optimize
```

## Resources

- [Laravel Zero Documentation](https://laravel-zero.com/docs) - Framework documentation
- [Laravel Zero GitHub](https://github.com/laravel-zero/laravel-zero) - Framework source code
- [Box Documentation](https://github.com/box-project/box) - PHAR building tool
- [Pest Documentation](https://pestphp.com) - Testing framework

---

For questions or issues, please open an issue on the [GitHub repository](https://github.com/hadefication/zeri/issues).
