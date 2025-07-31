# Zeri CLI

A CLI tool for managing AI development contexts. Zeri helps developers create and maintain AI-specific instruction files for their projects, generating optimized context files for Claude, Gemini, and Cursor IDE integration.

## Why Zeri?

When working with AI assistants on development projects, providing consistent, comprehensive context is crucial for getting quality results. Zeri solves this by:

- **Organizing project knowledge** into structured, reusable formats
- **Generating AI-optimized files** tailored for each tool's strengths
- **Maintaining consistency** across your development workflow
- **Saving time** by eliminating repetitive context setup
- **Improving AI responses** with well-structured, complete project information

## Features

- **Project Context Management**: Organize project information, standards, and workflows
- **AI-Specific Generation**: Create optimized instruction files for different AI tools
- **Smart Regeneration**: Only regenerate files when source content changes
- **Template-Based**: Consistent file creation with customizable templates
- **Specification Management**: Create and manage feature specifications
- **Self-Update**: Built-in update mechanism for easy maintenance
- **Cross-Platform**: Works on macOS, Linux, and Windows (via WSL)

## Installation

### Quick Install (Recommended)

**Install Script (Linux/macOS/Windows WSL)**
```bash
curl -fsSL https://raw.githubusercontent.com/hadefication/zeri/main/scripts/install.sh | bash
```

> **Windows Users**: Zeri requires a Unix-like environment. Please use [Windows Subsystem for Linux (WSL)](https://docs.microsoft.com/en-us/windows/wsl/install) to run Zeri on Windows.

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
./build.sh

# Install globally
sudo cp builds/zeri /usr/local/bin/zeri
```

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

Once the files are generated, you can share them with your AI assistant (Claude, Gemini, etc.) to help you fill in project-specific details, brainstorm feature specifications, or enhance your project documentation with context-aware suggestions.

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
- `--roadmap`: Include project roadmap section (disabled by default)

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
zeri add-spec "feature-name" [--path=/path/to/project] [--force]
```

**Options:**
- `--path`: Specify a different project directory
- `--force`: Overwrite existing specification with confirmation

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

### `self-update`
Update Zeri to the latest version.

```bash
zeri self-update [--check]
```

**Options:**
- `--check`: Check for updates without downloading

## Updating Zeri

### For Versions v1.2.0+
If you have Zeri v1.2.0 or later, you can use the built-in self-update feature:

```bash
zeri self-update
```

### For Older Versions (v1.0.1, v1.1.0)
If you have an older version without self-update capability, use our update script:

```bash
# Automatic update script
curl -sSL https://raw.githubusercontent.com/hadefication/zeri/main/scripts/update.sh | bash
```

Or update manually:

```bash
# Download latest version
curl -L https://github.com/hadefication/zeri/releases/latest/download/zeri -o zeri
chmod +x zeri

# Replace existing installation (find location with: which zeri)
sudo mv zeri $(which zeri)

# Verify update
zeri --version
```

**Update Script Features:**
- ✅ Automatically detects current installation
- ✅ Downloads and verifies latest release
- ✅ Creates backup before updating
- ✅ Restores backup if update fails
- ✅ Works with any installation method

**Options:**
```bash
# Force update even if on latest version
curl -sSL https://raw.githubusercontent.com/hadefication/zeri/main/scripts/update.sh | bash -s -- --force

# Show help
curl -sSL https://raw.githubusercontent.com/hadefication/zeri/main/scripts/update.sh | bash -s -- --help
```

## Directory Structure

When you run `zeri init`, the following structure is created:

```
.zeri/
├── project.md           # Project overview, tech stack & architecture
├── development.md       # Standards, decisions, patterns & workflows
├── specs/               # Feature specifications
└── templates/
    └── spec.md          # Feature specification template
```

## Generated Files

### Claude (CLAUDE.md)
Comprehensive, conversational format with full context optimized for Claude's reasoning style.

### Gemini (GEMINI.md) 
Directive, action-oriented format with clear rules and protocols optimized for Gemini.

### Cursor (.cursor/rules/)
- **zeri.mdc**: Unified development context, rules, and workflow guidelines

Concise .mdc format optimized for Cursor IDE integration with proper metadata headers.

## Examples

### Basic Workflow

```bash
# 1. Initialize project and generate AI files in one step
zeri init claude

# 2. Edit .zeri files to match your project needs
# Edit .zeri/project.md, .zeri/development.md

# 3. Add feature specifications
zeri add-spec "user-registration"
zeri add-spec "payment-processing"

# 4. Regenerate AI files after changes (if needed)
zeri generate claude --force

# 5. Use generated files with your AI tools
# - Copy CLAUDE.md content when working with Claude
# - Copy GEMINI.md content when working with Gemini  
# - Cursor will automatically use .cursor/rules/zeri.mdc file
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


## Development

For development setup, building from source, testing, and contributing guidelines, see [DEVELOPMENT.md](DEVELOPMENT.md).

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Contributing

Contributions are welcome! Please see [DEVELOPMENT.md](DEVELOPMENT.md) for setup instructions and contribution guidelines.

---

**Zeri** - Streamline your AI-assisted development workflow with organized, AI-optimized project context.
