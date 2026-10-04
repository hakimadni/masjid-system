# MasjidOS Gap Closure Execution Plan

> **For Hermes:** Execute milestone-by-milestone with real verification after each block. Update `10_KANBAN_BOARD.md` and `10_KANBAN_BOARD.html` after every milestone.

**Goal:** Close the currently known guide-vs-code gaps in MasjidOS, starting from authorization foundations and then finishing missing admin/domain surfaces.

**Architecture:** Use a foundation-first approach so later module work inherits the correct permission model, reusable UI patterns, and validation rules. Avoid duplicating CRUD work before route protection and shared UI primitives are stabilized.

**Tech Stack:** Laravel 12, Inertia.js, Vue 3, ShadCN-style UI components, SQLite/local dev DB.

---

## Found State

### Verified real state
- `routes/admin.php` is still primarily grouped by `role:` middleware.
- Request-level authorization exists for Finance/Donation create flows.
- `CheckPermission` middleware exists and is registered, but not broadly applied to admin routes yet.
- `User::hasPermissionTo()` now supports alias handling.
- Sidebar is now permission-aware with role fallback.
- `MoneyDisplay.vue` now exists and Finance page uses it.
- `jamaah` route still points to `PlaceholderModuleController::jamaah`.
- No dedicated `DonationCategory` model/table/UI exists.
- No shared `ConfirmDialog` component exists.
- Finance list/create/filter/status flows exist; edit/detail/delete safety still incomplete.
- Donation list/create/status/finance-link flows exist; detail/privacy/category gaps remain.

### Recovery / Deviation Note
The repo is not a clean greenfield implementation of the guide pack. Several modules are partially implemented and must be hardened instead of rebuilt. Status labels in the kanban were optimistic in places; all future promotion to `Done` must be backed by code/build/DB proof.

---

## Milestone 1 — Authorization Foundation Hardening

**Goal:** Make admin route protection guide-aligned enough that later CRUD work inherits proper permission gates.

### Task 1.1
- Audit each admin route group and map each route to permission slug(s).

### Task 1.2
- Refactor `routes/admin.php` so core routes use `permission:` middleware at route/group level, while preserving valid role fallback only where unavoidable.

### Task 1.3
- Add or tighten request authorization for update/destroy/status actions that currently rely only on route access.

### Task 1.4
- Verify with PHP syntax check + route inspection + build.

---

## Milestone 2 — Shared UI Pattern Gaps

**Goal:** Standardize destructive confirmations and shared page primitives.

### Task 2.1
- Add reusable `ConfirmDialog.vue` component using the existing dialog primitives.

### Task 2.2
- Replace inline destructive confirmation flows in Finance/Donation/Document (and any easy wins) with the shared confirm pattern.

### Task 2.3
- If feasible, add reusable `PageHeader.vue` component and adopt it in one or more admin pages.

### Task 2.4
- Verify with Vite build.

---

## Milestone 3 — Finance Gap Closure

**Goal:** Close the remaining Sprint 4/5 finance gaps.

### Task 3.1
- Add finance category management surface (index + create/update).

### Task 3.2
- Add finance transaction detail page.

### Task 3.3
- Add finance transaction edit flow restricted to draft/pending.

### Task 3.4
- Add deletion flow with explicit approved-transaction guard.

### Task 3.5
- Add dedicated pending-approval view or scoped screen if still required by guide.

### Task 3.6
- Re-verify finance report/balance logic and promote status only with proof.

---

## Milestone 4 — Donation Gap Closure

**Goal:** Close donation category, privacy, and detail gaps.

### Task 4.1
- Decide minimal guide-aligned category model: dedicated `donation_categories` table/model or explicit documented defer if guide truly allows free-text.
- Default assumption: implement dedicated category model because current kanban marks it missing.

### Task 4.2
- Add donation detail page.

### Task 4.3
- Add anonymous donor rule (`is_anonymous`) and UI/controller masking logic (`Hamba Allah`) without exposing private donor data publicly.

### Task 4.4
- Wire donation forms/listing to real category selection if category model is implemented.

---

## Milestone 5 — Jamaah Module MVP

**Goal:** Replace the Jamaah placeholder with a real minimal module.

### Task 5.1
- Add `jamaahs` migration/model.

### Task 5.2
- Add Jamaah index page.

### Task 5.3
- Add create/edit form.

### Task 5.4
- Add basic category/status filtering.

### Task 5.5
- Enforce sensitive-field visibility using `jamaah.sensitive.view`.

---

## Milestone 6 — Verification & Board Truth Pass

**Goal:** Re-run builds/syntax and update kanban truthfully.

### Task 6.1
- Run `php -l` on changed PHP files.
### Task 6.2
- Run `php artisan route:list` spot verification for protected routes.
### Task 6.3
- Run `npm run build`.
### Task 6.4
- Update `10_KANBAN_BOARD.md` statuses.
### Task 6.5
- Regenerate `10_KANBAN_BOARD.html` with a visible progress log.
### Task 6.6
- Update `HANDOFF.md` if any gaps remain.

---

## Execution Order
1. Milestone 1
2. Milestone 2
3. Milestone 3
4. Milestone 4
5. Milestone 5
6. Milestone 6

---

## Success Rule
A gap is only considered closed when the code exists, syntax/build passes, and the board note points to exact proof.