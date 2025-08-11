# Feature Specification: add-yes-flag-to-init-command

## Overview
Add a `--yes` flag to the `init` command to skip all interactive questions and use default values. This feature improves automation capabilities and allows for scripting the initialization process without requiring user interaction.

## Requirements
- Add `--yes` option to the init command signature
- When `--yes` flag is used, skip all interactive prompts (`ask()` and `confirm()`)
- Use sensible default values for all project configuration
- Display the default values being used when `--yes` is active
- Maintain backward compatibility with existing init command behavior
- Remove stack-specific defaults (e.g., PHP-specific values) to make the tool language-agnostic
- Work seamlessly with other flags like `--force`, `--roadmap`, and AI generation

## Implementation Notes
- Modified `InitCommand::$signature` to include `{--yes : Skip all questions and use defaults}`
- Added `$skipQuestions` variable to track the flag state
- Updated confirmation dialog to respect the `--yes` flag
- Replaced interactive `ask()` calls with default values when flag is present
- Changed default values to be generic rather than PHP/Laravel-specific:
  - Tech stack: "To be defined" instead of "PHP, Laravel"
  - Code style: Generic standards instead of PSR-12
  - Documentation: Generic API docs instead of PHPDoc
  - Patterns: Architecture-agnostic patterns
- Added informational output showing which defaults are being used
- Ensured compatibility with existing `--force` flag behavior for file overwriting

## TODO
- [x] Design and plan implementation
- [x] Implement core functionality
- [x] Remove PHP-specific default values
- [x] Add tests (manual testing completed)
- [x] Update documentation (help text automatically updated)
- [x] Review and refine
- [x] Mark specification as complete