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
```

### Development Commands

For development, use the `php application` command instead of the built `zeri` binary:

```bash
# Run commands during development
php application init
php application generate claude
php application add-spec "test-feature"

# After global installation, use:
zeri init
zeri generate claude
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

### Adding a New Command

1. Create command class in `app/Commands/`
2. Extend `LaravelZero\Framework\Commands\Command`
3. Define `$signature` and `$description` properties
4. Implement `handle()` method
5. Add to `config/commands.php` if needed (auto-discovery usually handles this)

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

1. Create generator class in `app/Generators/`
2. Extend `BaseGenerator`
3. Implement required methods
4. Create corresponding stub file in `stubs/`
5. Update `GenerateCommand::getGenerators()`

### Adding New Stub Templates

1. Create `.stub` file in `stubs/`
2. Use `{{PLACEHOLDER}}` format for replacements
3. Update generator to use `createFromStub()` method
4. Handle newline conversion with `\n` in placeholders

## Debugging

### Enable Verbose Output

```bash
php application generate claude -vvv
```

### Check Generated Files

During development, generated files are created in the current directory or specified path. Check:

- `CLAUDE.md`
- `GEMINI.md` 
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

### Version Management

Update version in:
- `config/app.php` - Application version
- `config/self-update.php` - Self-update version check

### Creating Releases

1. Update version numbers
2. Build production PHAR: `./build.sh`
3. Test executable: `./builds/zeri --version`
4. Create Git tag: `git tag v1.0.1`
5. Push: `git push origin v1.0.1`
6. Create GitHub release with binary attachment

## Contributing

### Code Style

This project uses Laravel Pint for code formatting:

```bash
./vendor/bin/pint
```

### Pull Request Process

1. Fork the repository
2. Create feature branch: `git checkout -b feature/new-feature`
3. Make changes and add tests
4. Run tests: `php application test`
5. Format code: `./vendor/bin/pint`
6. Build and test: `./build.sh && ./builds/zeri --version`
7. Commit and push changes
8. Create pull request

### Guidelines

- Follow PSR-12 coding standards
- Add tests for new functionality
- Update documentation for new features
- Keep commit messages clear and descriptive
- Ensure backward compatibility

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