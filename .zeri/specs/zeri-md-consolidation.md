# Feature Specification: zeri-md-consolidation

## Overview

Consolidate all zeri content into a single `.zeri/ZERI.md` file. This file contains:
- Project context (was in `project.md`)
- Development practices (was in `development.md`)
- AI instructions (was in generated AI file stubs)
- Spec references

AI files (CLAUDE.md, GEMINI.md, etc.) will only contain a reference to `.zeri/ZERI.md`.

This allows graceful integration with other AI tools (like Laravel Boost) that may also modify AI files.

## Requirements

### Core Functionality

- **`.zeri/ZERI.md`**: Single source file containing all project context and AI instructions
  - Created by `zeri init`
  - User edits this directly
  - Contains everything that was previously split across `project.md`, `development.md`, and AI file stubs

- **AI File Handling**:
  - If AI file exists: prepend (default) or append the reference line
  - If AI file doesn't exist: create minimal file with just the reference
  - Reference format: `Also read @.zeri/ZERI.md for project-specific context and instructions.`
  - Check for exact reference line before adding to prevent duplicates

- **Position Option**:
  - Default: prepend (zeri context first)
  - Option: `--position=append` (zeri context last)

### File Structure Changes

**Before (current):**
```
.zeri/
├── project.md          # Project overview
├── development.md      # Development practices
├── specs/
└── templates/
```

**After (new):**
```
.zeri/
├── ZERI.md             # Everything consolidated here
├── specs/
└── templates/
```

**Generated AI files:**
```
project-root/
├── CLAUDE.md           # Reference to .zeri/ZERI.md
├── GEMINI.md           # Reference to .zeri/ZERI.md
├── .cursor/rules/      # Reference to .zeri/ZERI.md
└── AGENTS.md           # Reference to .zeri/ZERI.md
```

### Idempotent Behavior

- Running `zeri generate` multiple times is safe
- Reference line only added if not already present (exact string match)
- Other tools can freely modify AI files; zeri only manages the reference line

### Backward Compatibility

- Clean break - no legacy mode
- `zeri generate` refuses to run with old structure, prompts user to run `zeri migrate`
- `zeri migrate` handles the transition automatically

### Migration Command

**Command:** `zeri migrate`

**Behavior:**
1. Detect old structure (`.zeri/project.md` + `.zeri/development.md`)
2. Merge contents into new `.zeri/ZERI.md`:
   - AI instructions section first
   - Project content second
   - Development content third
   - Spec references section
3. Remove old `project.md` and `development.md`
4. Display success message with next steps

**Options:**
- `--backup` - Keep copies of old files as `project.md.bak` and `development.md.bak`
- `--path=` - Specify project path (defaults to current directory)

**Error cases:**
- Already migrated (ZERI.md exists, old files don't) → inform user, exit 0
- Partial state (some files missing) → warn and attempt best-effort merge
- No .zeri directory → error, tell user to run `zeri init`

**Generate command check:**
When `zeri generate` detects old structure:
```
❌ Old zeri structure detected (project.md + development.md)

Run 'zeri migrate' to upgrade to the new consolidated format.
Optionally use 'zeri migrate --backup' to keep copies of old files.
```
Exit code: 1

## Implementation Notes

### Files to Modify

1. **Stubs:**
   - Create `stubs/ZERI.md.stub` - consolidated template (project + development + AI instructions)
   - Remove `stubs/project.md.stub`
   - Remove `stubs/development.md.stub`
   - Remove `stubs/CLAUDE.md.stub` (no longer needed - reference injection only)
   - Remove `stubs/GEMINI.md.stub` (no longer needed)
   - Remove `stubs/cursor-zeri.mdc.stub` (no longer needed)
   - Remove `stubs/AGENTS.md.stub` (no longer needed)

2. **Generators:**
   - Modify `app/Generators/BaseGenerator.php` - add reference injection logic
   - Modify `app/Generators/ClaudeGenerator.php` - inject reference only
   - Modify `app/Generators/GeminiGenerator.php` - inject reference only
   - Modify `app/Generators/CursorGenerator.php` - inject reference only
   - Modify `app/Generators/CodexGenerator.php` - inject reference only
   - Update `BaseGenerator::getZeriFiles()` to use `ZERI.md`

3. **Commands:**
   - Create `app/Commands/MigrateCommand.php` - migrate old structure to new
   - Modify `app/Commands/InitCommand.php` - create `ZERI.md` instead of `project.md` + `development.md`
   - Modify `app/Commands/GenerateCommand.php` - add `--position` option, add old structure check

### Reference Injection Logic

```php
protected function injectReference(string $filePath, string $position = 'prepend'): bool
{
    $reference = 'Also read @.zeri/ZERI.md for project-specific context and instructions.';

    if (File::exists($filePath)) {
        $content = File::get($filePath);

        // Check if reference already exists (exact match)
        if (str_contains($content, $reference)) {
            return false; // No change needed
        }

        // Inject reference
        if ($position === 'prepend') {
            $content = $reference . "\n\n" . $content;
        } else {
            $content = $content . "\n\n" . $reference;
        }

        File::put($filePath, $content);
        return true;
    } else {
        // Create minimal file with just reference
        File::put($filePath, $reference . "\n");
        return true;
    }
}
```

### .zeri/ZERI.md Content Structure

```markdown
# {{PROJECT_NAME}} - Project Context

## Instructions for AI Assistants

[AI workflow instructions - spec creation process, TODO best practices, etc.]

---

## Project Overview

[Project description, tech stack, architecture - was in project.md]

---

## Development Practices

[Code standards, patterns, workflows - was in development.md]

---

## Active Specifications

[Spec references - auto-generated section]

---

*Edit this file to update project context. Run `zeri generate <ai>` to update AI file references.*
```

## TODO

### Stubs
- [x] Create `stubs/ZERI.md.stub` (consolidated template)
- [x] Remove `stubs/project.md.stub`
- [x] Remove `stubs/development.md.stub`
- [x] Remove `stubs/CLAUDE.md.stub`
- [x] Remove `stubs/GEMINI.md.stub`
- [x] Remove `stubs/cursor-zeri.mdc.stub`
- [x] Remove `stubs/AGENTS.md.stub`

### Generators
- [x] Add `injectReference()` method to `BaseGenerator.php`
- [x] Add `hasOldStructure()` check to `BaseGenerator.php`
- [x] Update `ClaudeGenerator.php` - inject reference only
- [x] Update `GeminiGenerator.php` - inject reference only
- [x] Update `CursorGenerator.php` - inject reference only
- [x] Update `CodexGenerator.php` - inject reference only
- [x] Update `BaseGenerator::getZeriFiles()` to use `ZERI.md`

### Commands
- [x] Create `app/Commands/MigrateCommand.php`
- [x] Update `InitCommand.php` - create `ZERI.md` instead of separate files
- [x] Update `GenerateCommand.php` - add `--position` option, add old structure check

### Tests
- [x] Test `zeri migrate` merges files correctly
- [x] Test `zeri migrate --backup` keeps old files
- [x] Test `zeri generate` refuses with old structure
- [x] Test `zeri init` creates new structure
- [x] Test reference injection is idempotent
- [x] Test `--position=append` works correctly

### Documentation & Cleanup
- [x] Migrate this repo's `.zeri/` to new format
- [ ] Update README with new workflow
- [x] Run `./vendor/bin/pint` for code formatting
- [x] Run `./vendor/bin/pest` for tests
- [x] Review implementation against requirements
- [x] Mark specification as complete
