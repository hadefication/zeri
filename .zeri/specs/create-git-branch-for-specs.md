# Feature Specification: create-git-branch-for-specs

## Overview
Enhance the `add-spec` command to automatically create and switch to a new git branch when adding a new specification. This improves the development workflow by establishing a feature branch for implementing the specification, following git flow best practices.

## Requirements
- Detect if the current project is under git version control
- If non-git project, skip branch creation entirely and proceed with spec creation
- For git repositories: check for dirty working directory (uncommitted changes) before branch creation
- If dirty and no --force flag, suggest committing changes first and abort spec creation
- If dirty and --force flag provided, automatically stash changes with descriptive message
- Automatically create a new git branch when adding a specification (if in a clean git repository)
- Use the specification name as the branch name with appropriate formatting (e.g., feature/spec-name)
- Switch to the new branch after creation
- Add `--no-branch` flag to skip branch creation when desired (useful for git repos)
- Add `--force` flag to proceed with dirty working directory (automatically stashes changes)
- Handle existing branch names by appending pretty datetime (e.g., feature/spec-name-2024-jul-04-230pm)
- Provide clear feedback about git status, branch creation and switching
- Maintain full backward compatibility for non-git projects (no behavior change)

## Implementation Notes
- Modify `AddSpecCommand` to include git operations
- Add git detection logic using Laravel's `File` facade or `Process` to check for .git directory
- Flow: 1) Check if git repo 2) If non-git, skip branch logic entirely 3) If git, check dirty status
- For non-git projects: silently skip all git operations and proceed normally with spec creation
- Use `git status --porcelain` to detect dirty working directory (any output = dirty)
- If dirty and no --force flag, display helpful message: "Working directory has uncommitted changes. Please commit or stash changes before creating a spec branch, or use --force to auto-stash."
- If dirty and --force flag provided, stash changes with message: "zeri add-spec auto-stash: {spec-name} at {datetime}"
- Inform user when stashing occurs: "Stashing uncommitted changes before creating branch..."
- Provide recovery instructions: "To recover stashed changes later, use: git stash pop"
- Use Laravel Zero's `Process` facade for git commands (git checkout -b)
- Add branch name sanitization (convert spaces/special chars to hyphens, lowercase)
- Check if branch exists using `git rev-parse --verify branch-name` or `git show-ref --verify refs/heads/branch-name`
- If branch exists, append pretty datetime suffix: YYYY-MMM-DD-HHMMap format (e.g., feature/my-spec-2024-jul-04-230pm)
- Use PHP's `date('Y-M-d-ga')` for human-readable datetime formatting (Y=year, M=short month, d=day, g=12hr, a=am/pm)
- Format minutes to nearest 15-minute interval for cleaner appearance: 00, 15, 30, 45
- Add `--no-branch` and `--force` options to command signature
- Handle git errors gracefully (git command failures)
- Consider prefix options (feature/, spec/, etc.) - default to "feature/"
- Abort spec creation entirely if dirty and no --force flag (git repos only)
- When using --force with dirty directory: stash first, then create branch, ensuring clean workflow
- Test with various states: non-git projects, clean git, dirty git, existing branches

## TODO
- [x] Design and plan implementation
- [x] Implement core functionality
- [x] Add tests
- [x] Update documentation
- [x] Review and refine
- [x] Mark specification as complete