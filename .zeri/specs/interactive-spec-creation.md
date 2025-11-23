# Feature Specification: interactive-spec-creation

## Overview
Add AI assistant instructions to intelligently assess specification completeness when the user requests `add-spec`, and automatically ask targeted follow-up questions if the specification is incomplete. Inspired by Cursor's Plan Mode, this workflow enhancement will guide AI assistants (like Claude) to gather comprehensive requirements through clarifying questions before creating the specification file.

**Key Benefits:**
- AI intelligently identifies gaps in user's initial request
- Asks only relevant follow-up questions based on what's missing
- Reduces incomplete or ambiguous specifications
- Provides structured guidance through AI-user dialogue
- Improves consistency across project specifications
- No code changes needed - purely instructional enhancement

## Requirements

### AI Instruction Requirements
- **Intelligent Assessment**: When user requests to create a spec, AI MUST assess if the request contains sufficient detail
- **Conditional Q&A**: If specification is incomplete, AI MUST ask targeted follow-up questions
- **Identify Gaps**: AI should determine which areas need clarification:
  - Technical requirements (frameworks, versions, dependencies)
  - Architecture decisions (patterns, structure, integration points)
  - Implementation scope (features to include/exclude)
  - Testing requirements (unit, integration, e2e)
  - Performance considerations
  - Security requirements
- **Context Analysis**: AI should reference `.zeri/project.md` and `.zeri/development.md` to ask relevant questions
- **Iterative Dialogue**: AI can ask follow-up questions based on user answers until specification is complete
- **Only Ask What's Missing**: Don't ask questions if user already provided sufficient information
- **Create Spec When Complete**: Once AI has gathered sufficient information, run `add-spec` and populate the specification file
- **No Command Changes**: The `add-spec` command itself remains unchanged

### Specification Content Requirements
- **Complete Sections**: All sections (Overview, Requirements, Implementation Notes, TODO) should be thoroughly filled
- **Detailed TODO**: Create comprehensive, actionable TODO checklist based on gathered information
- **File References**: Include specific file paths and components that need modification
- **Technical Details**: Include frameworks, patterns, dependencies mentioned in Q&A

### Instruction Placement
- **CLAUDE.md**: Add instructions to the generated AI file
- **GEMINI.md**: Add instructions to the generated AI file
- **cursor-zeri.mdc**: Add instructions to the Cursor rules file
- **Consistency**: Ensure instructions are consistent across all AI platforms

## Implementation Notes

### Technical Considerations
- **Instruction-Only Feature**: No code changes to `add-spec` command needed
- **Documentation Update**: Add instructions to generated AI files (stubs)
- **Template Updates**: Update stub files for CLAUDE.md, GEMINI.md, cursor-zeri.mdc
- **Question Guidelines**: Provide sample questions and categories in instructions

### Files/Components to Modify
- `stubs/claude.stub` - Add AI instructions for spec creation workflow
- `stubs/gemini.stub` - Add AI instructions for spec creation workflow
- `stubs/cursor.stub` - Add AI instructions for spec creation workflow
- `.zeri/development.md` - Document the AI-assisted spec creation workflow (optional)

### Sample Question Categories to Include in Instructions

AI should assess user's initial request and ask questions only for missing information:

**Technical Requirements (ask if not specified):**
- What frameworks, libraries, or dependencies are involved?
- What versions or compatibility requirements exist?
- Are there any third-party integrations?

**Architecture (ask if not specified):**
- What design patterns should be followed?
- What existing components need modification?
- How does this integrate with existing systems?
- What files or directories will be affected?

**Scope (ask if ambiguous):**
- What features are in/out of scope for this implementation?
- Are there any edge cases to consider?
- What's the expected user workflow?

**Testing (ask if not mentioned):**
- What types of tests are needed (unit, integration, e2e)?
- What should the test coverage include?
- Are there specific test scenarios to cover?

**Performance & Security (ask if relevant):**
- Are there performance requirements or constraints?
- What security considerations apply?
- Are there any data privacy concerns?

**Implementation Details (ask if unclear):**
- Should this follow any existing patterns in the codebase?
- Are there any compatibility requirements with existing features?
- What's the expected timeline or priority?

### Testing Strategy
- Manual testing: Ask AI assistant to create specs and verify Q&A happens
- Verify generated specs are more complete than before
- Test across different AI platforms (Claude, Cursor, etc.)

### Future Enhancements (Out of Scope for v1)
- Add structured question bank in separate file AI can reference
- Command-line tool to help AI generate questions based on context
- Learn from past specifications to suggest better questions

## TODO
- [x] Draft AI instructions for intelligent spec creation workflow
- [x] Emphasize: AI must assess completeness and ask only about missing information
- [x] Include sample question categories organized by topic
- [x] Create workflow example: user request → AI assesses → asks questions → creates spec
- [x] Update `stubs/CLAUDE.md.stub` with new instructions
- [x] Update `stubs/GEMINI.md.stub` with new instructions
- [x] Update `stubs/cursor-zeri.mdc.stub` with new instructions
- [x] Add instructions to reference `.zeri/project.md` and `.zeri/development.md` during assessment
- [x] Specify that AI should populate the spec file after gathering information
- [ ] Test with vague request: "add a new feature" → verify AI asks follow-ups
- [ ] Test with detailed request: verify AI doesn't ask unnecessary questions
- [ ] Update `.zeri/development.md` to document the AI-assisted workflow (optional)
- [x] Generate updated AI files with new instructions
- [x] Run `./vendor/bin/pint` for code formatting
- [x] Build and test with `./build.sh`
- [ ] Verify across different AI platforms (Claude, Cursor)
- [ ] Review implementation against requirements
- [ ] Mark specification as complete