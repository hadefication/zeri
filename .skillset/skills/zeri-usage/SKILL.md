name: zeri-usage
description: Teach any AI agent how to run Zeri’s CLI workflow (init, add-spec, generate, self-update), enforce the specification-first process, and troubleshoot `.zeri` context files whenever a user asks how to use Zeri or needs help operating the tool.
---

# Zeri Usage

## Purpose

Use this skill whenever an AI agent needs to operate the Zeri CLI or explain its workflow to a human collaborator. The instructions assume Zeri is already installed (verify with `zeri --version`) and focus on keeping `.zeri/` sources authoritative while distributing the generated AI files.

## Operating Workflow

1. **Verify environment** – PHP 8.2+, Composer, and access to the installed `zeri` binary. In source checkouts, you may run `php application <command>` interchangeably.
2. **Initialize or inspect `.zeri/`** – `zeri init [ai]` bootstraps `ZERI.md`, `specs/`, and optionally AI outputs. Re-run with `--force` only when regenerating outputs.
3. **Maintain `.zeri/` sources** – Keep the single-source `.zeri/ZERI.md` up to date with project context and workflows, and manage feature specs inside `.zeri/specs/*.md`.
4. **Enforce spec workflow** – Every feature request must be formalized via `zeri add-spec`. Populate Overview, Requirements, Implementation Notes, and actionable TODOs immediately.
5. **Confirm reference sections** – Make sure each generated AI file has the latest reference section that points back to `.zeri/ZERI.md`. Once those references exist, the files auto-pull context at usage time and no longer need regeneration for routine `.zeri/` edits.
6. **Share with assistants** – Provide the correct file to each agent (details below) and remind users that AGENTS.md feeds Codex CLI, GitHub Copilot Workspace’s OpenCode agent, and any GPT-based client that consumes the standard agent brief.

## Command Cheat Sheet

### `zeri init [ai] [--path=dir] [--force] [--roadmap]`
- Creates `.zeri/` structure and optionally generates the requested AI file(s).
- Use `--roadmap` to include roadmap sections; `--force` to overwrite existing generated outputs.
- Development builds substitute `php application init`.

### `zeri add-spec "feature-name" [--path=dir] [--force] [--with-branch]`
- Run only after collecting requirements. Immediately fill all sections of the new spec file before coding.
- `--with-branch` creates a git branch per spec; useful for isolating workstreams.

### `zeri generate <ai|all> [--path=dir] [--force]`
- Use this to initially create AI instruction files or when their reference sections are missing/outdated.
- `--force` ensures a clean regeneration if outputs drift, templates change, or references were removed (routine `.zeri/` edits don’t require regeneration once references exist).

### `zeri self-update [--check]`
- Upgrades the installed PHAR. For source development, pull main, `composer install`, then run `./build.sh`.

## Specification Guardrails

- Never implement code without an approved `.zeri/specs/<feature>.md`.
- Keep TODO checkboxes accurate in real time; flip to `- [x]` the moment corresponding code lands.
- Before coding, restate the spec to the user and confirm they are ready to proceed.
- When requirements shift, update the existing spec rather than scattering notes elsewhere.

## Working in Repositories

- Use `php application <command>` during repo development; reserve the PHAR for installed environments.
- Treat `.zeri/ZERI.md` as the governing policy and reference it when uncertain about workflow expectations.
- Generated files live at the repo root; never edit them directly—regenerate instead.
- Format PHP changes with `./vendor/bin/pint` before committing (project rule).

## Sharing Agent Contexts

- `CLAUDE.md` → Claude (primary conversational assistant).
- `GEMINI.md` → Gemini or other directive-style LLMs.
- `.cursor/rules/zeri.mdc` → Cursor IDE auto-ingests this file.
- `AGENTS.md` → Consumed by OpenAI Codex CLI, GitHub Copilot Workspace’s OpenCode agent, and other GPT-based tools that accept a unified agent brief. Keep it in sync so every assistant receives consistent context.

## Troubleshooting

- **Outputs look stale** – Verify the reference sections are intact. If a file lost its reference block or templates changed, regenerate via `zeri generate all --force`; otherwise no action is needed.
- **Spec ambiguity** – Inspect the relevant `.zeri/specs/*.md`, clarify missing requirements with the user, then update the spec before proceeding.
- **Different working directory** – Add `--path=/desired/project` to commands when operating outside the repo root.
- **Need to confirm install path** – `which zeri` or `php application --version` verifies the executable in use.

## Trigger Examples

- “Add a new spec” → verify Zeri is installed (`zeri --version`), then run `zeri add-spec "name"`.
- “Add a new feature” / “implement feature ___” → confirm a spec exists; if not, create it via `zeri add-spec` before coding.
- “Regenerate the Claude and AGENTS files after editing `.zeri/ZERI.md`” → ensure reference sections still exist; regenerate only if they were removed.
