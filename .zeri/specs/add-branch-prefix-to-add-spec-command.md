# Spec: Add --branch-prefix option to add-spec command

## Overview
This feature adds a new option `--branch-prefix` to the `add-spec` command. This option will allow users to specify a custom prefix for the git branch that is automatically created when a new specification is added. This provides more flexibility for different branching strategies (e.g., `feature/`, `feat/`, `issue/`).

## Requirements
- The `add-spec` command should accept a new option: `--branch-prefix=<value>`.
- If the `--branch-prefix` option is provided, the value should be used as the prefix for the new branch name. For example, `php application add-spec "new-feature" --branch-prefix="issue/"` should create a branch named `issue/new-feature`.
- If the `--branch-prefix` option is not provided, the command should default to the existing `feature/` prefix.
- The branch name should still be slugified from the spec name.
- The help text for the `add-spec` command should be updated to include the new option.

## Implementation Notes
- Modify `app/Commands/AddSpecCommand.php`.
- Update the command signature to include the new `--branch-prefix` option with a default value.
- Update the `handle()` method to use the new option when constructing the branch name.
- No changes should be needed for the spec file generation itself.

## TODO
- [x] Update the signature of the `AddSpecCommand` to include the `--branch-prefix` option.
- [x] Implement the logic in the `handle()` method to use the custom prefix or the default.
- [x] Verify that the branch is created with the correct name when the option is used.
- [x] Verify that the branch is created with the default prefix when the option is omitted.
- [x] Update command documentation if any.
- [x] Run `./vendor/bin/pint` to format the code.
- [x] Run tests to ensure no regressions.
- [x] Mark specification as complete.