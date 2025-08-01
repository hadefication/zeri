# GEMINI DEVELOPMENT INSTRUCTIONS

**GENERATION DATE:** 2025-08-01 08:17:13

## REFERENCED FILES

**MANDATORY:** READ ALL REFERENCED FILES FOR COMPLETE CONTEXT

**CORE FILES:**
- [@.zeri/project.md](.zeri/project.md) → PROJECT OVERVIEW AND TECH STACK
- [@.zeri/development.md](.zeri/development.md) → STANDARDS, DECISIONS, PATTERNS, AND WORKFLOWS


**ACTIVE SPECIFICATIONS:**
- [@.zeri/specs/development-md-spec-instructions.md](.zeri/specs/development-md-spec-instructions.md) → DEVELOPMENT MD SPEC INSTRUCTIONS SPECIFICATION
- [@.zeri/specs/improve-add-spec-generated-file.md](.zeri/specs/improve-add-spec-generated-file.md) → IMPROVE ADD SPEC GENERATED FILE SPECIFICATION


---

## DEVELOPMENT DIRECTIVES

**MANDATORY REQUIREMENTS:**

1. **CODE QUALITY:** ALL code must follow established standards
2. **TESTING:** WRITE comprehensive tests for all features
3. **DOCUMENTATION:** UPDATE docs for any architectural changes
4. **SECURITY:** IMPLEMENT proper input validation and error handling
5. **PERFORMANCE:** OPTIMIZE for speed and resource efficiency
6. **SPECIFICATION DEPENDENCY:** ONLY implement or start coding when files in @.zeri/specs are referenced - NO code without specific feature specifications

**WORKFLOW COMPLIANCE:**

- CREATE feature branches for all changes
- FOLLOW established code review process
- ENSURE all tests pass before merging
- UPDATE relevant documentation

**PATTERN ADHERENCE:**

- USE established code patterns consistently
- MAINTAIN architectural consistency
- FOLLOW naming conventions
- IMPLEMENT proper error handling

---

## ⚠️ CRITICAL: SPECIFICATION CREATION PROTOCOL

**MANDATORY SPECIFICATION RULES - NEVER VIOLATE THESE:**

### SPECIFICATION CREATION MANDATE
**⚠️ MANDATORY: ALWAYS use `zeri add-spec <name>` to create new feature specifications.**

**NEVER MANUALLY CREATE SPECIFICATION FILES. USE THE COMMAND:**

```bash
# Create a new specification
zeri add-spec "feature-name"

# This creates .zeri/specs/feature-name.md with the standard template
```

### SPECIFICATION WORKFLOW REQUIREMENTS
1. **⚠️ REQUIRED: Create Specification**: ALWAYS use `zeri add-spec` command to create structured requirements
2. **Plan Implementation**: Break down requirements into actionable tasks
3. **Implement Features**: Follow the TODO checklist step by step
4. **Mark Progress**: Update TODOs in real-time during development
5. **Review and Complete**: Ensure all requirements are met

### IMPLEMENTATION PROCESS MANDATES
- **MANDATORY**: Always use `zeri add-spec` command - NEVER manually create .md files in .zeri/specs/
- ALWAYS start with a specification for non-trivial features
- BREAK complex features into smaller, manageable tasks
- FOLLOW established coding patterns and conventions
- WRITE tests alongside implementation

### ⚠️ TODO MARKING REQUIREMENTS
**ALWAYS mark TODO items as complete when implementing specifications in `.zeri/specs/`:**

- MARK checkboxes as `- [x]` when completing each implementation step
- THIS helps track progress and manage AI assistant usage limits
- ESSENTIAL for efficient development workflow with AI assistance
- UPDATE TODOs in real-time during implementation, NOT after completion

---

## CRITICAL REMINDERS

⚠️ **PRIMARILY edit files in the .zeri/ directory - NEVER remove zeri file references from generated AI files (GEMINI.md, CLAUDE.md, cursor-zeri.mdc)**
⚠️ **GENERATED AI files can be edited but PRESERVE all @.zeri/ file references and mandatory instructions**
⚠️ **NEVER bypass established workflows**
⚠️ **ALWAYS validate inputs and handle errors**
⚠️ **ENSURE backward compatibility**
⚠️ **FOLLOW security best practices**

---
*AUTO-GENERATED from .zeri configuration. Modify source files in .zeri/ to update.*