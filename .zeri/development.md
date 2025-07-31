# zeri - Development Practices

## Code Standards & Quality

### Code Style
Follow PSR-12 coding standards with Laravel Pint for formatting:
```bash
./vendor/bin/pint
```

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

### Framework Selection
- **Date**: Project inception
- **Decision**: Use Laravel Zero for CLI framework
- **Context**: Need robust CLI framework with built-in features
- **Options Considered**: Symfony Console, custom solution, Laravel Zero
- **Chosen Option**: Laravel Zero
- **Rationale**: Provides console features, dependency injection, testing framework, and PHAR building
- **Consequences**: Leverages Laravel ecosystem, established patterns

### Build System
- **Date**: Project inception  
- **Decision**: Use Box for PHAR creation
- **Context**: Need distributable single-file executable
- **Rationale**: Box is the standard for PHP PHAR building with compression and optimization
- **Consequences**: Easy distribution, but requires proper configuration

### Template System
- **Date**: Project inception
- **Decision**: Two-level template system (stubs + user templates)
- **Context**: Need flexibility for different project types while maintaining structure
- **Rationale**: Stubs for initial structure, templates for user customization
- **Consequences**: More complex but highly flexible system

---

## Code Patterns

### Command Pattern
All CLI commands extend `LaravelZero\Framework\Commands\Command`:
```php
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
```

### Generator Pattern
All generators extend `BaseGenerator`:
```php
abstract class BaseGenerator
{
    abstract protected function getOutputFileName(): string;
    abstract public function generate(): bool;
    public function getGeneratedFiles(): array { return [$this->getOutputFileName()]; }
}
```

### Template Processing
Use `{{PLACEHOLDER}}` format with `createFromStub()` method:
```php
$content = $this->createFromStub('template.stub', [
    'PROJECT_NAME' => $projectName,
    'DESCRIPTION' => $description
]);
```

### Error Handling
- Use Laravel Zero's built-in error handling
- Return appropriate exit codes (0 for success, non-zero for errors)
- Provide user-friendly error messages

### Testing Patterns
- Feature tests for command functionality in `tests/Feature/`
- Unit tests for individual components in `tests/Unit/`
- Use Pest testing framework

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
4. **Format code** - `./vendor/bin/pint`
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

### Code Review Guidelines
- All code must be reviewed before merge
- Check for PSR-12 compliance
- Verify tests pass and cover new functionality
- Ensure backward compatibility
- Review security implications

### Build Process
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

### Performance Debugging
- Profile file operations for large projects
- Monitor memory usage during template processing
- Optimize string operations for large files

### Resolution Documentation
- Document fixes in commit messages
- Update DEVELOPMENT.md for recurring issues
- Share solutions in GitHub issues/discussions