# zeri - Development Practices

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