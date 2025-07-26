# Zeri CLI

A Laravel Zero CLI tool for managing AI development contexts. Zeri helps developers create and maintain AI-specific instruction files for their projects, generating optimized context files for Claude, Gemini, and Cursor.

## Features

- **Project Context Management**: Organize project information, standards, and workflows
- **AI-Specific Generation**: Create optimized instruction files for different AI tools
- **Smart Regeneration**: Only regenerate files when source content changes
- **Template-Based**: Consistent file creation with customizable templates
- **Cross-Platform**: Works on macOS, Linux, and Windows

## Installation

### Quick Install (Recommended)

**Composer (Global)**
```bash
composer global require hadefication/zeri
```

**Install Script (Linux/macOS)**
```bash
curl -fsSL https://raw.githubusercontent.com/hadefication/zeri/main/scripts/install.sh | bash
```

### Manual Installation

**Download Binary**
```bash
# Download latest release
curl -L https://github.com/hadefication/zeri/releases/latest/download/zeri > /usr/local/bin/zeri
chmod +x /usr/local/bin/zeri
```

**From Source**
```bash
# Clone the repository
git clone https://github.com/hadefication/zeri.git
cd zeri

# Install dependencies
composer install

# Build the executable
php application app:build

# Install globally
sudo cp builds/zeri /usr/local/bin/zeri
```

### Package Managers

| Platform | Command |
|----------|---------|
| **Composer** (Global) | `composer global require hadefication/zeri` |
| **APT** (Ubuntu/Debian) | *Coming soon* |
| **YUM/DNF** (RHEL/Fedora) | *Coming soon* |
| **Pacman** (Arch) | *Coming soon* |
| **Chocolatey** (Windows) | *Coming soon* |

### Requirements

- PHP 8.2 or higher
- Composer (for global installation)


## Quick Start

```bash
# Initialize a new project
zeri init

# Or initialize and auto-generate AI files
zeri init claude

# Add a feature specification
zeri add-spec "user-authentication"

# Generate AI instruction files (if not done during init)
zeri generate all

# Generate specific AI file
zeri generate claude
zeri generate gemini
zeri generate cursor
```

## Commands

### `init`
Initialize the `.zeri` directory structure in your project.

```bash
zeri init [ai] [--path=/path/to/project] [--force]
```

**Arguments:**
- `ai`: Optional AI type to auto-generate after init (`claude`, `gemini`, `cursor`, or `all`)

**Options:**
- `--path`: Specify a different directory (default: current directory)
- `--force`: Force regeneration of AI files even if they already exist

**Examples:**
```bash
# Basic initialization
zeri init

# Initialize and generate Claude files
zeri init claude

# Initialize and generate all AI files
zeri init all

# Force regeneration of existing AI files
zeri init claude --force
```

### `add-spec <name>`
Create a new feature specification file.

```bash
zeri add-spec "feature-name" [--path=/path/to/project]
```

**Options:**
- `--path`: Specify a different project directory

### `generate <ai>`
Generate AI-specific instruction files.

```bash
zeri generate <ai> [options]
```

**Arguments:**
- `ai`: AI type (`claude`, `gemini`, `cursor`, or `all`)

**Options:**
- `--path`: Specify project directory
- `--force`: Force regeneration even if files are up to date

## Directory Structure

When you run `zeri init`, the following structure is created:

```
.zeri/
├── context.md           # Project overview & tech stack
├── standards.md         # Code style & best practices
├── workflows/
│   ├── coding.md        # Development task template
│   ├── planning.md      # Feature planning template
│   └── debugging.md     # Debugging workflow
├── project/
│   ├── roadmap.md       # Current priorities & goals
│   ├── decisions.md     # Architecture decisions log
│   └── patterns.md      # Common code patterns
├── specs/               # Feature specifications
└── templates/
    ├── task.md          # Generic task template
    └── spec.md          # Feature specification template
```

## Generated Files

### Claude (CLAUDE.md)
Comprehensive, conversational format with full context optimized for Claude's reasoning style.

### Gemini (GEMINI.md) 
Directive, action-oriented format with clear rules and protocols optimized for Gemini.

### Cursor (.cursor/rules)
Concise format focused on immediate coding context for IDE integration.

## Examples

### Basic Workflow

```bash
# 1. Initialize project and generate AI files in one step
zeri init claude

# 2. Edit .zeri files to match your project needs
# Edit .zeri/context.md, .zeri/standards.md, etc.

# 3. Add feature specifications
zeri add-spec "user-registration"
zeri add-spec "payment-processing"

# 4. Regenerate AI files after changes (if needed)
zeri generate claude --force

# 5. Use generated files with your AI tools
# - Copy CLAUDE.md content when working with Claude
# - Copy GEMINI.md content when working with Gemini  
# - Cursor will automatically use .cursor/rules
```

### Working with Different Projects

```bash
# Work on a specific project
zeri generate all --path=/path/to/my-project

# Force regeneration of all files
zeri generate all --force

# Generate Claude files
zeri generate claude
```

## Customization

### Templates
Edit the template files in `.zeri/templates/` to customize the format of new specifications and tasks.

### Stubs
The application uses stub files located in `stubs/` for initial file creation. You can modify these to change the default content structure.

## Development

### Running Tests
```bash
php application test
```

### Building Executable

**Recommended (using build script):**
```bash
./build.sh
```

**Manual build:**
```bash
php application app:build
```

*Note: For development, use `php application <command>`. After global installation, use `zeri <command>`.*

The executable will be created in `builds/zeri`.

## Requirements

- PHP 8.1 or higher
- Composer

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

*Built with [Laravel Zero](https://laravel-zero.com) - A micro-framework for building console applications.*
