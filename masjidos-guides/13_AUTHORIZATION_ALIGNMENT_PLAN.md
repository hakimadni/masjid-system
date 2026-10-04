# Authorization Alignment Plan — MasjidOS

> Source of truth: `05_ROLE_PERMISSION_GUIDE.md`, `07_DEVELOPMENT_RULES.md`, `11_ACCEPTANCE_TESTING.md`

## Goal
Menormalkan aplikasi dari role-heavy routing menjadi **permission-enforced server-side authorization** tanpa merusak fitur yang sudah jalan.

## Found State
Berdasarkan audit repo saat ini:
- `routes/admin.php` masih dominan memakai `role:...`
- request authorization seperti `AdminFinanceStoreRequest` dan `AdminDonationStoreRequest` masih `return true`
- frontend sidebar `resources/js/lib/adminNavigation.js` masih memakai `roles`, bukan permission-aware visibility
- modul inti (finance, donation, document, settings, zakat, wakaf, reports) sudah ada dan berjalan, jadi migrasi harus **incremental**
- build frontend lolos, migrations applied, beberapa modul sudah jauh di depan kanban

## Non-Goals
- Tidak rewrite total auth system
- Tidak menambah fitur bisnis baru
- Tidak mengubah domain model besar di luar authorization layer

## Sprint Outcome Definition
Sprint ini dianggap selesai jika:
- semua route admin sensitif dilindungi oleh permission server-side
- request `authorize()` tidak lagi `true` untuk mutation penting
- super admin tetap punya akses penuh
- unauthorized role gagal mengakses mutation penting
- sidebar visibility mulai mengikuti permission minimal untuk modul utama
- minimal acceptance check untuk finance/donation/settings/documents/reports berjalan

---

## Workstream A — Permission Mapping & Safe Migration

### A1. Audit current permission seed data vs guide matrix
**Files:**
- `database/seeders/RolePermissionSeeder.php`
- `database/seeders/AdminMvpSeeder.php`
- `app/Models/Permission.php`
- `app/Models/Role.php`

**Tasks:**
1. Bandingkan permission yang ada dengan daftar guide:
   - `finance.view/create/update/delete/approve/reject/report/proof.view`
   - `donation.view/create/update/delete/confirm/reject/report`
   - `document.view/manage/private.view`
   - `report.view/export/public.manage`
   - `setting.view/manage`
   - `zakat.view/manage`
   - `wakaf.view/manage`
   - `audit_log.view`
2. Tandai missing permission.
3. Patch seeder agar permission list lengkap dan idempotent.

**Done when:**
- permission matrix di seeders minimal match guide untuk modul aktif.

### A2. Define transition rule: route role guard stays temporarily, permission becomes authoritative
**Why:**
Untuk mengurangi risiko, gunakan dual-layer sementara:
- `auth` + `verified` tetap
- role group boleh tetap sementara sebagai coarse guard
- permission checks ditambahkan sebagai fine-grained enforcement

**Done when:**
- documented in code comments / plan notes
- migration path jelas untuk developer berikutnya

---

## Workstream B — Backend Authorization Enforcement

### B1. Add request authorization for finance mutations
**Files:**
- `app/Http/Requests/AdminFinanceStoreRequest.php`
- potentially new request(s) for finance status update if needed

**Target rules:**
- create finance => `finance.create`
- create approved transaction directly should require `finance.approve` or be blocked
- update status approve => `finance.approve`
- update status reject => `finance.reject`

**Done when:**
- `authorize()` no longer returns unconditional true
- unauthorized users get 403

### B2. Add request authorization for donation mutations
**Files:**
- `app/Http/Requests/AdminDonationStoreRequest.php`
- status update request if introduced

**Target rules:**
- create donation => `donation.create`
- confirm donation => `donation.confirm`
- reject donation => `donation.reject`

### B3. Add permission enforcement in controllers/routes for module reads
**Files:**
- `routes/admin.php`
- controllers for:
  - `FinanceController`
  - `DonationController`
  - `DocumentController`
  - `ReportController`
  - `SettingsController`
  - `ZakatController`
  - `WakafController`
  - `AuditLogController`

**Recommended approach:**
- add route middleware by permission where straightforward
- or use controller checks (`abort_unless`, `authorize`) for actions with different permission per method

**Minimum permission map:**
- finance index => `finance.view`
- document index => `document.view`
- report index => `report.view`
- report export => `report.export`
- settings index => `setting.view`
- settings update => `setting.manage`
- zakat index => `zakat.view`
- zakat mutations => `zakat.manage`
- wakaf index => `wakaf.view`
- wakaf mutations => `wakaf.manage`
- audit logs => `audit_log.view`

### B4. Protect sensitive admin data access
**Files:**
- `DocumentController.php`
- `AuditLogController.php`
- `ZakatController.php`
- public controllers if needed

**Target rules:**
- document private access => `document.private.view`
- mustahik-sensitive views should not be broadly exposed
- finance proof access must eventually map to `finance.proof.view`

---

## Workstream C — Sidebar / Frontend Permission Awareness

### C1. Add permission-aware nav helper
**Files:**
- `resources/js/lib/adminNavigation.js`
- auth shared props source if needed (`HandleInertiaRequests.php`)

**Plan:**
1. Ensure frontend receives user permission slugs.
2. Extend nav items with `permissions` field.
3. Keep existing `roles` as temporary fallback only if needed.
4. Prefer permission checks for visible menu entries.

**Done when:**
- finance menu requires `finance.view`
- donation menu requires `donation.view`
- document menu requires `document.view`
- reports menu requires `report.view`
- settings menu requires `setting.view`/`setting.manage`

---

## Workstream D — Acceptance & Safety Checks

### D1. Manual acceptance matrix
Use `11_ACCEPTANCE_TESTING.md` for these specific checks:

**Finance**
- authorized user can create transaction
- unauthorized user gets blocked
- unauthorized user cannot approve/reject
- approved transaction stores metadata

**Donation**
- authorized user can create donation
- unauthorized user blocked
- confirm/reject permission works

**Settings**
- user without settings permission cannot update mosque settings

**Reports**
- user without report export permission cannot export

**Documents**
- document list/create blocked correctly for unauthorized roles

### D2. Regression safety
Run after changes:
- `php artisan migrate --pretend`
- `php artisan test` (if stable enough)
- `npm run build`
- `php -l` on touched PHP files

---

## Suggested Task Order
1. Patch permission seeders to match guide.
2. Expose permission slugs to frontend shared props.
3. Add request `authorize()` checks for finance and donation.
4. Add route/controller permission checks for reports, settings, documents.
5. Add route/controller permission checks for zakat/wakaf/audit logs.
6. Update sidebar visibility to permissions.
7. Run acceptance checks and fix fallout.
8. Update kanban notes.

---

## Risks
- Existing users/seed data may not have complete permissions until reseeded.
- Over-tightening permissions can temporarily lock users out.
- Some modules currently depend on broad role access and may need staged rollout.

## Mitigation
- Keep `super-admin` full access at all times.
- Use additive permission checks first.
- Validate with seeded admin accounts after every phase.

---

## Deliverables
- patched permission seeders
- route/controller/request permission enforcement
- permission-aware admin navigation
- acceptance checklist results
- kanban update notes

## Recommended Execution Label
Treat this as:
**Sprint 13A — Permission & Authorization Alignment**

It should be completed before any major new module work continues.
