# Feature Specification: context-usage-limits-instruction

## Overview
Introduce an explicit "Context & Usage Limits" workflow inside `.zeri/ZERI.md` (and the source stub that feeds it) so every generated AI context file tells assistants what to do when they are close to exhausting conversation context. The workflow should instruct assistants to pause when ~10–20% context remains, summarize progress into the active specification, sync TODOs, and only resume after capturing the summary. This keeps long-running tasks aligned with the spec-first process and prevents silent context loss.

## Requirements
- Add a clearly labeled "Context & Usage Limits" (or similarly titled) subsection to `.zeri/ZERI.md` under the instructions/workflow area so it is impossible to miss while reading the master context file.
- Describe the trigger condition: when an assistant estimates that only 10–20% of tokens/context are free, they must pause execution.
- Detail the required actions when the trigger fires:
  - Summarize current progress into the active `.zeri/specs/<feature>.md` file (Overview/Implementation Notes/TODO as appropriate).
  - Update the spec's TODO checklist so completed work is marked and new follow-ups are captured before continuing.
  - Resume coding or planning only after the summary is saved and context is refreshed (e.g., after a new thread or trimmed history).
- Propagate the exact same instructions into the stub `stubs/ZERI.md.stub` so any new project initialized or regenerated inherits the workflow automatically.
- Ensure language fits existing tone/style guidelines in `ZERI.md` (concise, directive, specification-led) and references the spec workflow already described in the document.
- Do not modify generated AI files (AGENTS.md, CLAUDE.md, etc.); those files will naturally pick up the change the next time generators run.

## Implementation Notes
- Primary files: `.zeri/ZERI.md` (live instructions) and `stubs/ZERI.md.stub` (source template). Keep both perfectly in sync.
- Place the new subsection near the existing specification workflow guidance so readers encounter it while planning implementations.
- Reuse existing markdown hierarchy (e.g., `### Context & Usage Limits`) and follow the established directive voice.
- Consider adding bullet points for the trigger and each required step to make scanning easy for AI assistants in constrained contexts.
- No automated tests exist for documentation; verification will come from manual review and, optionally, regenerating AI files to confirm the text appears.

## TODO
- [x] Outline the new "Context & Usage Limits" copy and review tone/style for `.zeri/ZERI.md`
- [x] Update `.zeri/ZERI.md` with the finalized subsection
- [x] Mirror the subsection into `stubs/ZERI.md.stub`
- [x] (Optional) Regenerate AI context files to validate propagation (deferred; next scheduled `php application generate <ai>` run will naturally pick up the change)
- [x] Review wording with stakeholders and confirm spec completeness
- [x] Mark specification as complete once merged
