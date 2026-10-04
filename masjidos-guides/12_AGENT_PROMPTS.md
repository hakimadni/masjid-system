# Agent Prompts — MasjidOS

Gunakan prompt ini untuk mengarahkan AI coding agent agar patuh ke guide.

## Prompt 1 — Initial Compliance Prompt

```text
You are my senior fullstack engineer for MasjidOS.

Before coding, read all markdown files in the project guide folder:
- 00_README.md
- 01_PRODUCT_SPEC.md
- 02_MODULE_SCOPE.md
- 03_ARCHITECTURE_GUIDE.md
- 04_DATABASE_GUIDE.md
- 05_ROLE_PERMISSION_GUIDE.md
- 06_UI_UX_SHADCN_GUIDE.md
- 07_DEVELOPMENT_RULES.md
- 08_AI_AGENT_WORKFLOW.md
- 09_SPRINT_ROADMAP.md
- 10_KANBAN_BOARD.md
- 11_ACCEPTANCE_TESTING.md

Treat these files as the source of truth.

Your first task is only to audit the current codebase and report:
1. Current stack and architecture
2. Existing auth and role system
3. Existing database/migrations/models
4. Existing frontend layout/component system
5. Existing ShadCN Vue availability
6. Gaps versus the guide files
7. Recommended implementation plan
8. Risks
9. First sprint/task to execute

Do not write production code before finishing this audit.
```

## Prompt 2 — Sprint Execution Prompt

```text
Continue MasjidOS development by following the guide files.

Current sprint: [WRITE SPRINT NUMBER AND NAME]
Current task IDs from kanban: [WRITE TASK IDS]

Rules:
- Read 09_SPRINT_ROADMAP.md and 10_KANBAN_BOARD.md first.
- Only work on the selected sprint/task IDs.
- Do not implement features outside the selected sprint.
- Follow architecture, database, role, UI/UX, and development rules from the markdown guide files.
- Use ShadCN Vue components for UI where applicable.
- Use Indonesian labels in UI.
- Keep changes incremental.
- After coding, update 10_KANBAN_BOARD.md task status.

Before coding, provide a short implementation plan:
1. Files to inspect
2. Files likely to create/modify
3. Database impact
4. Permission impact
5. UI impact
6. Manual test plan

After coding, report:
1. What was implemented
2. Files created
3. Files modified
4. Migrations added
5. Commands to run
6. Manual test checklist
7. Risks/known issues
8. Next recommended task
```

## Prompt 3 — Bug Fix Prompt

```text
Fix a bug in MasjidOS while following the markdown guide files.

Bug description:
[WRITE BUG]

Rules:
- Reproduce or inspect the bug first.
- Identify root cause.
- Do not make unrelated changes.
- Protect finance/donation data integrity.
- Keep UI consistent with ShadCN Vue.
- Update acceptance checklist if needed.
- Update kanban if this bug belongs to an active sprint.

Output:
1. Root cause
2. Fix summary
3. Files changed
4. Commands/tests run
5. Manual verification steps
6. Any remaining risk
```

## Prompt 4 — MVP Hardening Prompt

```text
Audit and harden the MasjidOS MVP.

Read:
- 07_DEVELOPMENT_RULES.md
- 10_KANBAN_BOARD.md
- 11_ACCEPTANCE_TESTING.md

Focus on:
- Finance calculation accuracy
- Donation confirmation linkage
- Server-side permission checks
- Dashboard summary correctness
- Sensitive data protection
- UI empty/loading/error states
- Mobile responsiveness
- Build/lint/test status

Do not add new major modules. Fix MVP quality first.

Output:
1. MVP health summary
2. Critical bugs
3. Security/data risks
4. Fix plan by priority
5. Implemented fixes
6. Remaining issues
7. Updated kanban status
```

## Prompt 5 — Public Portal Prompt

```text
Continue MasjidOS with the Public Portal phase.

Read:
- 01_PRODUCT_SPEC.md
- 02_MODULE_SCOPE.md
- 03_ARCHITECTURE_GUIDE.md
- 06_UI_UX_SHADCN_GUIDE.md
- 09_SPRINT_ROADMAP.md
- 10_KANBAN_BOARD.md
- 11_ACCEPTANCE_TESTING.md

Current target:
- Sprint 14: Public Portal Foundation
- Sprint 15: Public Donation & Transparency

Rules:
- Public pages must not require login.
- Public pages must not expose private data.
- Public financial report must show aggregate only.
- Do not expose transaction proof, internal notes, donor phone/email, mustahik data, or internal documents.
- UI should be clean, trustworthy, and mosque-appropriate.

Start with Sprint 14 only unless approved to continue.
```

## Prompt 6 — Zakat/Wakaf/Qurban Prompt

```text
Continue MasjidOS with advanced Islamic operation modules.

Read all guide files first, especially:
- 02_MODULE_SCOPE.md
- 04_DATABASE_GUIDE.md
- 05_ROLE_PERMISSION_GUIDE.md
- 09_SPRINT_ROADMAP.md
- 10_KANBAN_BOARD.md
- 11_ACCEPTANCE_TESTING.md

Target module:
[WRITE ZAKAT / WAKAF / QURBAN]
Target sprint:
[WRITE SPRINT NUMBER]

Rules:
- Keep calculation manual unless requirements are explicit.
- Protect mustahik/private donor data.
- Link finance transactions only when business rule is clear.
- For qurban sapi, support max 7 shares.
- Do not overbuild barcode/payment gateway unless requested.
- Use ShadCN Vue UI patterns.
- Update kanban after implementation.
```
