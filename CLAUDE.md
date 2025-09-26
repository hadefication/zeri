# Development Context for Claude

This file provides context for Claude to assist with development tasks.
Generated on: 2025-09-26 08:14:25

## Referenced Files

Please read the following project files for complete context:

**Core Configuration:**
- [@.zeri/project.md](.zeri/project.md) - Project overview, tech stack, and architecture
- [@.zeri/development.md](.zeri/development.md) - Standards, decisions, patterns, and development workflows

---

## Instructions for Claude

When working on this project:

1. **Follow the established patterns** described in the patterns section
2. **Adhere to coding standards** outlined in the standards section
3. **Consider architectural decisions** when proposing changes
4. **Follow the development workflow** for implementation steps
5. **Reference current priorities** to align with project goals
6. **Check active specifications** for feature-specific requirements
7. **ONLY implement or start coding when files in .zeri/specs are referenced** - Do not write code without specific feature specifications

## ⚠️ Specification Workflow

**Always use `zeri add-spec <name>` to create new feature specifications—never create these files manually.**

```bash
# Create a new specification
zeri add-spec "feature-name"

# This creates .zeri/specs/feature-name.md with the standard template
```

Follow this process every time:
1. Run the command above to create the specification.
2. Plan the implementation by breaking requirements into actionable tasks.
3. Implement work step by step, updating the TODO checklist in real time.
4. Mark each TODO item as soon as its implementation step finishes using `- [x]`.
5. Review the specification with the user, confirm it is complete, and explicitly ask whether it is ready for implementation before writing code.

**Critical Reminders**
- Do not implement any specification unless the user explicitly instructs you to begin coding.
- When generating or revising specs as an AI assistant, ensure the specification is complete and ask the user if it is ready for implementation.
- Keep all actionable tasks inside the TODO section; keep Overview, Requirements, and Implementation Notes descriptive only.
- Maintain detailed TODO entries so progress survives token limits or session resets.
- Update TODOs in real time—mark each checkbox immediately after its implementation step finishes so progress is never batched at the end.

**TODO Best Practices**
```markdown
## TODO
- [ ] Analyze requirements and design implementation approach
- [ ] Identify files and components that need modification
- [ ] Implement core functionality following established patterns
- [ ] Write comprehensive tests for new functionality
- [ ] Update relevant documentation and help text
- [ ] Run tests and ensure code formatting compliance
- [ ] Review implementation against requirements
- [ ] Mark specification as complete
```

## Key Reminders

- Primarily edit files in the .zeri/ directory - NEVER remove zeri file references from generated AI files (CLAUDE.md, GEMINI.md, cursor-zeri.mdc)
- Generated AI files can be edited but preserve all .zeri/ file references and mandatory instructions
- Always write tests for new functionality
- Follow the established code review process
- Consider performance and security implications
- Update documentation when making architectural changes
- Communicate clearly about implementation decisions

---
*This context file is automatically generated from your .zeri configuration. Update the source files in .zeri/ to modify this content.*
