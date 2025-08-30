# Feature Specification: add-codex-openai-support

## Overview
Add support for OpenAI Codex AI assistant by creating a new generator that produces context files optimized for Codex's code understanding and generation capabilities. This extends zeri's multi-AI platform support to include OpenAI's Codex model.

## Requirements
- Create new CodexGenerator class following existing generator patterns
- Generate AGENTS.md context file (official Codex CLI project context file format)
- Add Codex-specific stub template in stubs/ directory
- Integrate with existing generate command to support `zeri generate codex`
- Include Codex-optimized project context, development practices, and code patterns
- Support same .zeri/ file references as other generators (project.md, development.md, specs/)
- Maintain consistency with existing AI platform implementations
- Follow AGENTS.md format requirements for Codex CLI compatibility

## Implementation Notes
- Follow existing generator pattern by extending BaseGenerator
- Create stubs/agents.md.stub template (note: AGENTS.md is the official filename)
- Update GenerateCommand::getGenerators() to include CodexGenerator
- Generator should output to AGENTS.md file (CodexGenerator::getOutputFileName() returns 'AGENTS.md')
- Codex CLI uses AGENTS.md for "Memory with project docs" functionality
- Include structured code context with clear separation of concerns
- Consider Codex's strengths in code completion and generation
- Test with sample project to ensure proper context generation and Codex CLI compatibility

## TODO
- [x] Design and plan implementation
- [x] Implement core functionality
- [x] Add tests
- [x] Update documentation
- [x] Review and refine
- [x] Mark specification as complete