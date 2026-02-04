# Feature Specification: Replace --force with --replace in Generate Command

## Overview
Replace the misleading `--force` flag in `zeri generate` with a `--replace` flag. Since the generate command now uses idempotent reference injection (prepend/append), `--force` does nothing. The new `--replace` flag will completely overwrite AI files with a fresh reference block, requiring user confirmation.

## Requirements
- Remove `--force` flag from `generate` command
- Add `--replace` flag that completely overwrites AI files
- Require user confirmation before replacing (unless `--yes` is provided)
- Show warning about what will be lost (any custom content in the AI file)
- Update README documentation
- Remove unused `--backup` and `--interactive` flags (also do nothing currently)

## Implementation Notes
- Files to modify:
  - `app/Commands/GenerateCommand.php` - Update signature and handle logic
  - `app/Generators/BaseGenerator.php` - Add replace logic
  - `app/Generators/ClaudeGenerator.php` - Use replace parameter
  - `app/Generators/GeminiGenerator.php` - Use replace parameter
  - `app/Generators/CursorGenerator.php` - Use replace parameter
  - `app/Generators/CodexGenerator.php` - Use replace parameter
  - `README.md` - Update documentation

## TODO
- [x] Update GenerateCommand signature (remove --force, --backup, --interactive; add --replace, --yes)
- [x] Update BaseGenerator with replace logic
- [x] Update all generators to use replace parameter
- [x] Add confirmation prompt before replacing
- [x] Update README documentation
- [x] Run tests
- [x] Mark specification as complete
