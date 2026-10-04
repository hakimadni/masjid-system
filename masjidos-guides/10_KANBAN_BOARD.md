# Kanban Board — MasjidOS

Gunakan file ini sebagai tracking utama. AI agent wajib update status task setelah selesai.

## Status Legend

- `Backlog` = belum dikerjakan
- `Ready` = siap dikerjakan
- `In Progress` = sedang dikerjakan
- `Review` = perlu dicek user/dev
- `Done` = selesai
- `Blocked` = terhambat

## Kanban Policy

- Maksimal 2 task `In Progress` dalam satu waktu.
- Jangan mulai sprint baru jika sprint sebelumnya masih ada blocker critical.
- Task besar harus dipecah.
- Setelah task selesai, tambahkan notes singkat.
- Jangan hapus task lama; ubah statusnya.

---

## Sprint 0 — Repository Audit & Project Setup

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S0-01 | Audit backend framework and version | Done | AI | Verified PHP 8.4.8, Composer 2.8.8. |
| S0-02 | Audit frontend framework and version | Done | AI | Verified Node 22.22.3, npm 10.9.8, Vite build passes. |
| S0-03 | Audit auth system | Done | AI | Verified auth + verified middleware usage across `routes/web.php` and `routes/admin.php` for admin flows. |
| S0-04 | Audit role/permission existing logic | Done | AI | Repo audited; actual code is role-heavy and not fully aligned with permission guide. |
| S0-05 | Audit routes and admin layout | Done | AI | Admin/public routes and layout structure inspected. |
| S0-06 | Audit database migrations/models | Done | AI | Migrations inspected; sqlite schema applied through documents/wakaf/zakat. |
| S0-07 | Audit ShadCN Vue availability | Done | AI | ShadCN-style component tree verified in use across admin/public pages. |
| S0-08 | Produce technical implementation plan | Done | AI | Delivered audit gap table plus `13_AUTHORIZATION_ALIGNMENT_PLAN.md` for corrective execution. |
| S0-09 | Produce guide-vs-code gap table by file (Point A) | Done | AI | Delivered precise file-level alignment/gap mapping against guide pack. |
| S0-10 | Write authorization alignment plan | Done | AI | See `13_AUTHORIZATION_ALIGNMENT_PLAN.md` for Sprint 13A corrective plan. |

---

## Sprint 1 — Auth, Roles, Permissions Foundation

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S1-01 | Reuse or create roles table/model | Done | AI | Roles model/table verified in repo and active DB state. |
| S1-02 | Seed default roles | Done | AI | Re-seeded and verified role/permission population including guide-aligned additions. |
| S1-03 | Add permission helper or middleware | Done | AI | `User::hasPermissionTo()` plus Inertia `permission_slugs` sharing are implemented and verified. |
| S1-04 | Protect admin routes | Done | AI | Admin routes now use permission middleware gates for module/action access; verified route registration and syntax. |
| S1-05 | Add role-based sidebar visibility | Done | AI | Sidebar visibility now supports permission-aware access with role fallback via `resources/js/lib/adminNavigation.js`. |
| S1-06 | Verify Super Admin full access | Done | AI | Verified `super-admin` receives all seeded permissions in DB state after re-seed. |
| S1-07 | Verify unauthorized access blocked | Review | AI | Controller checks and route middleware applied; needs manual verification. |

---

## Sprint 2 — Core Database & Seeders

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S2-01 | Create finance_categories migration/model | Done | AI | Migration/model verified in `create_admin_core_tables` and `app/Models/FinanceCategory.php`. |
| S2-02 | Create financial_transactions migration/model | Done | AI | Migration/model verified in `create_admin_core_tables` and existing finance transaction model usage. |
| S2-03 | Create donation_categories migration/model | Done | AI | Migration/model verified in `2026_06_13_200000_add_donation_categories_and_jamaahs.php` and `app/Models/DonationCategory.php`. |
| S2-04 | Create donations migration/model | Done | AI | Donation tables/models verified in migration and `app/Models/Donation.php`. |
| S2-05 | Create schedules migration/model | Done | AI | Prayer/service schedule tables and models verified. |
| S2-06 | Create events migration/model | Done | AI | Event migration/model verified. |
| S2-07 | Create jamaahs migration/model | Done | AI | Migration/model verified in `2026_06_13_200000_add_donation_categories_and_jamaahs.php` and `app/Models/Jamaah.php`. |
| S2-08 | Create assets migration/model | Done | AI | Asset migration/model verified. |
| S2-09 | Create documents migration/model | Done | AI | Documents migration/model verified. |
| S2-10 | Create announcements migration/model | Done | AI | Announcement migration/model verified. |
| S2-11 | Create default category seeders | Done | AI | `AdminMvpSeeder.php` seeds default finance categories and baseline admin demo data. |
| S2-12 | Run migration and seeder validation | Done | AI | Migrations exist, seeder executed successfully, and DB state was verified during review. |

---

## Sprint 3 — Admin Layout & ShadCN UI Shell

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S3-01 | Create or improve admin sidebar layout | Done | AI | `AuthenticatedLayout.vue` provides a substantial admin shell/sidebar layout. |
| S3-02 | Create mobile sidebar Sheet/drawer | Done | AI | Mobile sheet/drawer flow exists via `SheetPanel` in `AuthenticatedLayout.vue`. |
| S3-03 | Add topbar and user menu | Done | AI | Topbar and `UserMenu` are implemented in authenticated layout. |
| S3-04 | Add PageHeader component | Done | AI | Reusable `resources/js/Components/ui/page/PageHeader.vue` created and used in Finance and Media pages. |
| S3-05 | Add StatusBadge component | Done | AI | `resources/js/Components/ui/status/StatusBadge.vue` exists. |
| S3-06 | Add MoneyDisplay component | Done | AI | Added reusable `resources/js/Components/ui/display/MoneyDisplay.vue` for shared currency formatting. |
| S3-07 | Add EmptyState component | Done | AI | `resources/js/Components/ui/empty/EmptyState.vue` exists. |
| S3-08 | Add ConfirmDialog pattern | Done | AI | Reusable `resources/js/Components/ui/dialog/ConfirmDialog.vue` created; Finance reject and Media delete now use shared pattern. |

---

## Sprint 4 — Finance Module Part 1

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S4-01 | Finance category list page | Done | AI | Finance category list page now exists: `resources/js/Pages/Admin/FinanceCategories/Index.vue`. |
| S4-02 | Finance category create/edit | Done | AI | FinanceCategoryController + create/edit routes + page implemented. |
| S4-03 | Financial transaction list page | Done | AI | Finance transaction list page exists, was re-verified in `resources/js/Pages/Admin/Finance/Index.vue`, and build passes. |
| S4-04 | Create income form | Done | AI | Shared create transaction form supports `income` flow and was re-verified in Finance page. |
| S4-05 | Create expense form | Done | AI | Shared create transaction form supports `expense` flow and was re-verified in Finance page. |
| S4-06 | Edit draft/pending transaction | Done | AI | Edit page + route (`finance.edit`) implemented; edit blocked for approved transactions. |
| S4-07 | Transaction detail page | Done | AI | Detail page + route (`finance.show`) implemented. |
| S4-08 | Finance filters date/category/status/type | Done | AI | Controller + UI both implement search, date, category, status, and type filters. |
| S4-09 | Server-side finance validation | Done | AI | `AdminFinanceStoreRequest` enforces finance validation plus category/entry-type consistency and permission-aware authorization. |

---

## Sprint 5 — Finance Module Part 2

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S5-01 | Implement FinanceService summary logic | Backlog | AI | Summary logic still lives in FinanceController; no FinanceService class found. |
| S5-02 | Add approve transaction action | Done | AI | `FinanceController::updateStatus` sets approved_by/approved_at; approve ActionMenu confirmed in Finance UI. |
| S5-03 | Add reject transaction action | Done | AI | Rejection Dialog with rejected_reason confirmed in Finance UI; controller clears approval metadata on reject. |
| S5-04 | Restrict approved transaction deletion | Done | AI | Delete guard implemented in `FinanceController::destroy`; test verifies approved transactions cannot be deleted even by super-admin. |
| S5-05 | Add pending approval screen | Done | AI | Pending approval page now exists: `resources/js/Pages/Admin/Finance/Pending.vue` + route `finance.pending`. |
| S5-06 | Add finance report page | Review | AI | `ReportController::index` compiles income/expense totals and recent transactions; full acceptance still pending. |
| S5-07 | Verify official balance calculation | Review | AI | Income totals are approved-only; expense calculation not independently confirmed without full ReportController review. |

---

## Sprint 6 — Donation Module

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S6-01 | Donation category list/create/edit | Done | AI | DonationCategories migration/model/controller/page verified; DonationCategoryController with CRUD routes/UI implemented. |
| S6-02 | Donation list page | Done | AI | Donation list page + summary cards confirmed in `resources/js/Pages/Admin/Donations/Index.vue`. |
| S6-03 | Create manual donation form | Done | AI | Manual donation form confirmed with donor fields, amount, method, status, and date. |
| S6-04 | Donation detail page | Done | AI | Created dedicated donation detail page, wired routes, and tested confirm/reject. |
| S6-05 | Confirm donation action | Done | AI | Confirm action calls `updateStatus` with status=confirmed; `syncFinanceTransaction` runs automatically. |
| S6-06 | Reject donation action | Done | AI | Reject action calls `updateStatus`; linked finance transaction deleted and finance_transaction_id cleared. |
| S6-07 | Link confirmed donation to finance income | Done | AI | `syncFinanceTransaction` confirmed in `DonationController` — creates approved finance income linked to donation. |
| S6-08 | Donation summary cards | Done | AI | Summary cards (collected_total, pending_total, donor_total, records_total) confirmed on donations index. |
| S6-09 | Anonymous donor display rule | Done | AI | `donations.is_anonymous` column added; `Donor::maskedName()` / `maskedPhone()` implemented; `DonationController::serializeDonation()` masks donor when user lacks `donation.sensitive.view`. |

---

## Sprint 7 — Dashboard MVP

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S7-01 | Saldo Kas card | Review | AI | Dashboard exists; card presence/accuracy requires acceptance verification. |
| S7-02 | Monthly income card | Review | AI | Dashboard/report logic exists; acceptance pending. |
| S7-03 | Monthly expense card | Review | AI | Dashboard/report logic exists; acceptance pending. |
| S7-04 | Monthly donation card | Review | AI | Dashboard/report logic exists; acceptance pending. |
| S7-05 | Pending approval card | Review | AI | Pending summary logic exists in finance; dashboard acceptance pending. |
| S7-06 | Latest transactions section | Review | AI | Dashboard/report sections appear implemented; not fully verified here. |
| S7-07 | Latest donations section | Review | AI | Dashboard/report sections appear implemented; not fully verified here. |
| S7-08 | Upcoming schedules section | Review | AI | Dashboard/schedule integration appears present; not fully verified here. |
| S7-09 | Upcoming events section | Review | AI | Event integration appears present; not fully verified here. |
| S7-10 | Active announcements section | Review | AI | Announcement integration appears present; not fully verified here. |

---

## Sprint 8 — Schedule Module

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S8-01 | Schedule list page | Review | AI | Schedule module exists in repo. |
| S8-02 | Create schedule form | Review | AI | Prayer/service create routes and requests exist. |
| S8-03 | Edit schedule form | Review | AI | Status/update flows exist; full edit acceptance not verified. |
| S8-04 | Delete schedule with confirm dialog | Done | AI | `ScheduleController::destroyPrayer` + `destroyService`, `schedules.destroy-prayer` + `schedules.destroy-service`, and shared `ConfirmDialog` implemented. |
| S8-05 | Filter schedule by type/date | Review | AI | Needs explicit acceptance verification. |
| S8-06 | Upcoming schedule component | Review | AI | Appears integrated in dashboard/public planning; not fully verified. |

---

## Sprint 9 — Event Module

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S9-01 | Event list page | Review | AI | Event admin page exists. |
| S9-02 | Create event form | Review | AI | Event create flow exists. |
| S9-03 | Edit event form | Review | AI | Event update/status flow exists. |
| S9-04 | Event detail page | Done | AI | `EventController::show` method and `resources/js/Pages/Admin/Events/Show.vue` created, linked via ActionMenu detail button in Events index. |
| S9-05 | Event status handling | Review | AI | Published status used in public portal and admin flow. |
| S9-06 | Upcoming events dashboard integration | Review | AI | Public/dashboard event integration appears implemented; acceptance pending. |

---

## Sprint 10 — Jamaah Module

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S10-01 | Jamaah list page | Done | AI | Jamaah list page exists: `resources/js/Pages/Admin/Jamaahs/Index.vue` + `JamaahController::index`. |
| S10-02 | Create jamaah form | Done | AI | Create form integrated in Jamaah index page; `JamaahController::store` with validation. |
| S10-03 | Edit jamaah form | Done | AI | Edit form integrated in Jamaah index page; `JamaahController::update` with permission gate. |
| S10-04 | Jamaah detail page | Done | AI | Implemented `Show.vue` detail view with full data visualization. |
| S10-05 | Jamaah category filter | Done | AI | Added `category` migration, updated model, and implemented filter in `Index.vue` and controller. |
| S10-06 | Mustahik sensitive data protection | Done | AI | Implemented robust sensitive data masking rule in controller for `mustahik` category. |

---

## Sprint 11 — Asset & Document Module

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S11-01 | Asset list page | Review | AI | Asset module exists in repo. |
| S11-02 | Create asset form | Review | AI | Asset create flow exists. |
| S11-03 | Edit asset form | Review | AI | Asset status/update flow exists; full edit acceptance pending. |
| S11-04 | Asset detail page | Done | AI | `AssetController::show`, `assets.show` route, `Assets/Show.vue` implemented with detail view. |
| S11-05 | Asset condition summary | Review | AI | Report aggregation for asset conditions exists. |
| S11-06 | Document list page | Review | AI | Documents index page exists. |
| S11-07 | Upload/create document | Review | AI | Upload/create flow exists with file validation. |
| S11-08 | Document detail/download | Done | AI | `DocumentController::show`/`download`, `Documents/Show.vue` with download button, action menu in index page. |
| S11-09 | Document permission protection | Done | AI | `document.delete` permission added to seeder, route separated into `document.delete` middleware group, `documents.destroy` route gated. |

---

## Sprint 12 — Announcement & Basic Reports

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S12-01 | Announcement list page | Review | AI | Announcement page exists. |
| S12-02 | Create announcement form | Review | AI | Create flow exists. |
| S12-03 | Edit announcement form | Review | AI | Update/status flow exists. |
| S12-04 | Publish/archive status | Review | AI | Published status used by public portal. |
| S12-05 | Copy WhatsApp-ready text | Backlog | AI |  |
| S12-06 | Donation report page | Review | AI | Reports module includes donation summary data; page completeness pending. |
| S12-07 | Asset report page | Review | AI | Reports module includes asset condition data; page completeness pending. |
| S12-08 | Print-friendly report layout | Backlog | AI |  |

---

## Sprint 13 — MVP Hardening

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S13-01 | Run full manual MVP test | Backlog | AI/User |  |
| S13-02 | Fix critical finance bugs | Done | AI | Fixed critical SQL bug in Finance report export column querying. |
| S13-03 | Fix permission gaps | Done | AI | Applied explicit permission checks for finance/donation status endpoints via middleware and controller blocks. |
| S13-04 | Improve mobile responsiveness | Backlog | AI |  |
| S13-05 | Improve loading/empty/error states | Backlog | AI |  |
| S13-06 | Run build/lint/test | Review | AI | npm build passes; PHP syntax checks pass; full test/lint sweep not complete. |
| S13-07 | Prepare MVP release notes | Backlog | AI |  |

---

## Sprint 14 — Public Portal Foundation

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S14-01 | Public layout | Review | AI | Public layout exists and builds. |
| S14-02 | Public home page | Review | AI | Public home page exists. |
| S14-03 | Public mosque profile page | Review | AI | Public profile page exists. |
| S14-04 | Public schedule page | Review | AI | Public schedule page exists but still placeholder-grade. |
| S14-05 | Public events page | Review | AI | Public events page exists. |
| S14-06 | Public announcements page | Review | AI | Public announcements page exists. |
| S14-07 | Public contact section | Review | AI | Public contact page exists. |
| S14-08 | Verify no private data exposure | Review | AI | Code audit suggests aggregate-only direction, but privacy acceptance remains incomplete. |

---

## Sprint 15 — Public Donation & Transparency

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S15-01 | Public donation page | Review | AI | Public donation page exists. |
| S15-02 | Donation instruction/settings display | Review | AI | UI exists, but controller currently returns placeholder/empty settings. |
| S15-03 | Public financial summary page | Review | AI | Public finance report exists. |
| S15-04 | Income/expense aggregate by category | Backlog | AI | Public report currently returns empty category breakdown arrays. |
| S15-05 | Hide donor private data | Backlog | AI |  |
| S15-06 | Hide proof/internal finance notes | Review | AI | Public report uses aggregate data; final privacy verification still needed. |

---

## Sprint 16 — Settings Module

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S16-01 | Mosque profile settings | Review | AI | Settings controller/page exists for basic mosque profile fields. |
| S16-02 | Donation account settings | Backlog | AI | Public donation page expects this, but settings flow is not clearly implemented. |
| S16-03 | Public portal visibility settings | Review | AI | Settings toggles exist for public portal visibility. |
| S16-04 | Report settings | Backlog | AI |  |
| S16-05 | Settings permission protection | Backlog | AI | Still role-based, not permission-grade. |

---

## Sprint 17 — Zakat Module

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S17-01 | Zakat migrations/models | Done | AI | Zakat tables/models exist and migrations are applied. |
| S17-02 | Zakat dashboard | Review | AI | Zakat index/summary page exists. |
| S17-03 | Input zakat payment | Review | AI | Zakat muzakki input flow exists. |
| S17-04 | Muzakki list | Review | AI | Muzakki listing exists. |
| S17-05 | Mustahik list with privacy | Backlog | AI |  |
| S17-06 | Zakat distribution records | Review | AI | Distribution records flow exists. |
| S17-07 | Zakat report | Backlog | AI |  |

---

## Sprint 18 — Wakaf Module

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S18-01 | Wakaf migrations/models | Done | AI | Wakaf tables/models exist and migrations are applied. |
| S18-02 | Wakaf list page | Review | AI | Wakaf index page exists. |
| S18-03 | Create wakaf record | Review | AI | Wakaf create flow exists. |
| S18-04 | Wakif data handling | Review | AI | Wakif data flow exists. |
| S18-05 | Wakaf purpose/status tracking | Review | AI | Status/purpose tracking exists; acceptance pending. |
| S18-06 | Wakaf report | Backlog | AI |  |

---

## Sprint 19 — Qurban Module Part 1

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S19-01 | Qurban participant migrations/models | Backlog | AI |  |
| S19-02 | Participant list page | Backlog | AI |  |
| S19-03 | Create participant form | Backlog | AI |  |
| S19-04 | Animal type selection | Backlog | AI |  |
| S19-05 | Sapi 1/7 share logic | Backlog | AI |  |
| S19-06 | Payment tracking | Backlog | AI |  |
| S19-07 | Link payment to finance if required | Backlog | AI |  |

---

## Sprint 20 — Qurban Module Part 2

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S20-01 | Qurban animal migrations/models | Backlog | AI |  |
| S20-02 | Animal list page | Backlog | AI |  |
| S20-03 | Assign animal to sapi group | Backlog | AI |  |
| S20-04 | Slaughter status tracking | Backlog | AI |  |
| S20-05 | Distribution tracking | Backlog | AI |  |
| S20-06 | Coupon code generation | Backlog | AI |  |
| S20-07 | Print qurban coupon | Backlog | AI |  |
| S20-08 | Qurban report | Backlog | AI |  |

---

## Sprint 21 — Export, Audit Log, Production Readiness

| ID | Task | Status | Owner | Notes |
|---|---|---|---|---|
| S21-01 | Finance report export | Done | AI | CSV export verified and critical bug querying non-existent column fixed. |
| S21-02 | Donation report export | Backlog | AI |  |
| S21-03 | Audit log migration/model | Done | AI | Audit log table/model exist. |
| S21-04 | Audit finance actions | Review | AI | Finance controller writes audit logs. |
| S21-05 | Audit donation actions | Review | AI | Donation controller writes audit logs. |
| S21-06 | Add/improve tests | Backlog | AI |  |
| S21-07 | Security review | Backlog | AI |  |
| S21-08 | Final UI polish | Backlog | AI |  |
