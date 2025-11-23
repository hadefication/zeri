# GEMINI DEVELOPMENT INSTRUCTIONS

**GENERATION DATE:** 2025-11-23 15:28:25

## REFERENCED FILES

**MANDATORY:** READ ALL REFERENCED FILES FOR COMPLETE CONTEXT

**CORE FILES:**

@.zeri/project.md
@.zeri/development.md

---

## DEVELOPMENT DIRECTIVES

**MANDATORY REQUIREMENTS:**

1. **CODE QUALITY:** ALL code must follow established standards
2. **TESTING:** WRITE comprehensive tests for all features
3. **DOCUMENTATION:** UPDATE docs for any architectural changes
4. **SECURITY:** IMPLEMENT proper input validation and error handling
5. **PERFORMANCE:** OPTIMIZE for speed and resource efficiency
6. **SPECIFICATION DEPENDENCY:** ONLY implement or start coding when files in .zeri/specs are referenced - NO code without specific feature specifications

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

## ⚠️ Specification Workflow

**Always use `zeri add-spec <name>` to create new feature specifications—never create these files manually.**

```bash
# Create a new specification
zeri add-spec "feature-name"

# This creates .zeri/specs/feature-name.md with the standard template
```

### Intelligent Spec Creation Process

When a user requests to create a specification, you MUST follow this process:

1. **Assess Completeness**: Analyze the user's request to determine if it contains sufficient detail for a complete specification.

2. **Ask Targeted Follow-up Questions**: If the request is incomplete, ask clarifying questions ONLY about missing information. Reference `.zeri/project.md` and `.zeri/development.md` to ask relevant questions based on project context.

   **Question Categories (ask only what's missing):**

   - **Technical Requirements** (if not specified):
     - What frameworks, libraries, or dependencies are involved?
     - What versions or compatibility requirements exist?
     - Are there any third-party integrations?

   - **Architecture** (if not specified):
     - What design patterns should be followed?
     - What existing components need modification?
     - How does this integrate with existing systems?
     - What files or directories will be affected?

   - **Scope** (if ambiguous):
     - What features are in/out of scope for this implementation?
     - Are there any edge cases to consider?
     - What's the expected user workflow?

   - **Testing** (if not mentioned):
     - What types of tests are needed (unit, integration, e2e)?
     - What should the test coverage include?
     - Are there specific test scenarios to cover?

   - **Performance & Security** (if relevant):
     - Are there performance requirements or constraints?
     - What security considerations apply?
     - Are there any data privacy concerns?

   - **Implementation Details** (if unclear):
     - Should this follow any existing patterns in the codebase?
     - Are there any compatibility requirements with existing features?
     - What's the expected timeline or priority?

3. **Iterative Dialogue**: Continue asking follow-up questions based on user answers until you have gathered sufficient information for a complete specification.

4. **Create Specification**: Once you have comprehensive information, run `zeri add-spec "feature-name"` and populate ALL sections with detailed content based on the gathered information.

5. **Plan Implementation**: Break requirements into actionable tasks in the TODO section.

6. **Update TODOs in Real Time**: Mark each TODO item as soon as its implementation step finishes using `- [x]`.

7. **Review Before Coding**: Confirm the specification is complete with the user and explicitly ask whether it is ready for implementation before writing code.

**Critical Reminders**
- Do not implement any specification unless the user explicitly instructs you to begin coding.
- When user's request is vague (e.g., "add a new feature"), you MUST ask clarifying questions before creating the spec.
- When user's request is detailed, you may proceed directly to creating the spec without unnecessary questions.
- Populate the specification with comprehensive details from your Q&A dialogue.
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

---

## CRITICAL REMINDERS

⚠️ **PRIMARILY edit files in the .zeri/ directory - NEVER remove zeri file references from generated AI files (GEMINI.md, CLAUDE.md, cursor-zeri.mdc)**
⚠️ **GENERATED AI files can be edited but PRESERVE all .zeri/ file references and mandatory instructions**
⚠️ **NEVER bypass established workflows**
⚠️ **ALWAYS validate inputs and handle errors**
⚠️ **ENSURE backward compatibility**
⚠️ **FOLLOW security best practices**

---
*AUTO-GENERATED from .zeri configuration. Modify source files in .zeri/ to update.*
