# Sprint Roadmap — MasjidOS

Roadmap ini memecah pengembangan menjadi sprint kecil agar AI agent tidak mengerjakan fitur terlalu besar sekaligus.

## Sprint 0 — Repository Audit & Project Setup

### Goal

Memahami repo dan menyiapkan dasar kerja.

### Tasks

- Audit stack backend/frontend.
- Audit auth dan role existing.
- Audit routes dan layout.
- Audit database migrations.
- Audit component system.
- Tentukan MVP technical plan.
- Pastikan ShadCN Vue availability atau rencana install minimal.

### Deliverables

- Architecture audit summary.
- Implementation plan.
- Risk list.
- First migration/page plan.

### Definition of Done

- Agent paham struktur repo.
- Tidak ada coding besar sebelum audit.
- Kanban Sprint 0 updated.

---

## Sprint 1 — Auth, Roles, Permissions Foundation

### Goal

Menyiapkan role access untuk admin panel.

### Tasks

- Reuse or create roles.
- Seed roles.
- Add permission helper/policy/middleware if needed.
- Protect admin routes.
- Role-based sidebar visibility.
- Super Admin access verified.

### Deliverables

- Role foundation.
- Sidebar permission gating.
- Basic route protection.

### Definition of Done

- Unauthorized user tidak bisa akses admin modules.
- Super Admin bisa akses semua.
- Sidebar mengikuti role/permission.

---

## Sprint 2 — Core Database & Seeders

### Goal

Membuat struktur data MVP.

### Tasks

- Create migrations for finance categories.
- Create migrations for financial transactions.
- Create migrations for donation categories.
- Create migrations for donations.
- Create migrations for schedules.
- Create migrations for events.
- Create migrations for jamaahs.
- Create migrations for assets.
- Create migrations for documents.
- Create migrations for announcements.
- Create default seeders.

### Deliverables

- MVP tables.
- Default categories.
- Relationship basics.

### Definition of Done

- Migrations run cleanly.
- Seeders idempotent.
- No existing data loss.

---

## Sprint 3 — Admin Layout & ShadCN UI Shell

### Goal

Membuat admin UI foundation modern.

### Tasks

- Sidebar layout.
- Mobile drawer sidebar.
- Topbar.
- Breadcrumb/page header pattern.
- Reusable status badge.
- Reusable money display.
- Reusable empty state.
- Reusable confirm dialog.

### Deliverables

- Consistent admin shell.
- Navigation for MVP modules.

### Definition of Done

- Admin layout usable desktop/mobile.
- Menu active state jelas.
- No broken navigation.

---

## Sprint 4 — Finance Module Part 1: Category & Transaction CRUD

### Goal

Bendahara bisa mencatat pemasukan/pengeluaran.

### Tasks

- Finance category list/create/edit.
- Transaction list.
- Create income.
- Create expense.
- Edit draft/pending transaction.
- Transaction detail.
- Filter by date/category/status/type.
- Validation server-side.

### Deliverables

- Finance CRUD core.

### Definition of Done

- Amount valid.
- Category type matches transaction type.
- List/filter works.
- UI uses ShadCN pattern.

---

## Sprint 5 — Finance Module Part 2: Approval & Report

### Goal

Keuangan menjadi auditable dan official balance akurat.

### Tasks

- Approve transaction.
- Reject transaction.
- Restrict approved deletion.
- Finance summary calculation.
- Basic finance report page.
- Pending approval screen.

### Deliverables

- Approval workflow.
- Official balance based on approved transactions.

### Definition of Done

- Only approved affects balance.
- Approval metadata stored.
- Unauthorized users cannot approve/reject.

---

## Sprint 6 — Donation Module

### Goal

Donasi manual bisa dicatat dan dikonfirmasi.

### Tasks

- Donation category CRUD/list.
- Donation list.
- Create manual donation.
- Donation detail.
- Confirm donation.
- Reject donation.
- Link confirmed donation to finance income.
- Donation summary.

### Deliverables

- Donation management.

### Definition of Done

- Confirmed donation creates/links income.
- Rejected donation does not affect finance.
- Anonymous display rule prepared.

---

## Sprint 7 — Dashboard MVP

### Goal

Dashboard menampilkan ringkasan operasional utama.

### Tasks

- Saldo Kas card.
- Pemasukan bulan ini card.
- Pengeluaran bulan ini card.
- Donasi bulan ini card.
- Pending approvals card.
- Upcoming schedules.
- Upcoming events.
- Latest transactions.
- Latest donations.
- Active announcements.

### Deliverables

- Dashboard usable untuk DKM.

### Definition of Done

- Values accurate.
- Uses approved finance only.
- Empty state clear.

---

## Sprint 8 — Schedule Module

### Goal

Jadwal imam, muadzin, khatib, kajian, dan marbot rapi.

### Tasks

- Schedule list.
- Create schedule.
- Edit schedule.
- Delete schedule.
- Filter by type/date.
- Upcoming schedule component.

### Deliverables

- Schedule management.

### Definition of Done

- Schedule CRUD works.
- Filter works.
- Role access enforced.

---

## Sprint 9 — Event Module

### Goal

Kegiatan masjid bisa dikelola.

### Tasks

- Event list.
- Create event.
- Edit event.
- Event detail.
- Status draft/published/completed/cancelled.
- Optional poster upload if upload infra exists.

### Deliverables

- Event management.

### Definition of Done

- Event CRUD works.
- Upcoming events appear in dashboard.

---

## Sprint 10 — Jamaah Module

### Goal

Data jamaah dasar bisa dikelola aman.

### Tasks

- Jamaah list.
- Create jamaah.
- Edit jamaah.
- Jamaah detail.
- Category management by fixed enum/string.
- Sensitive view rule for mustahik.

### Deliverables

- Jamaah data management.

### Definition of Done

- Mustahik data protected.
- Unauthorized users cannot view sensitive info.

---

## Sprint 11 — Asset & Document Module

### Goal

Inventaris dan dokumen dasar tercatat.

### Tasks

- Asset CRUD.
- Asset condition summary.
- Document CRUD/upload if supported.
- Document type filter.

### Deliverables

- Inventory and document management.

### Definition of Done

- Assets manageable.
- Documents role-protected.

---

## Sprint 12 — Announcement & Basic Report Polish

### Goal

Pengumuman dan laporan dasar siap dipakai.

### Tasks

- Announcement CRUD.
- Publish/archive status.
- Public/private flag.
- Copy WhatsApp-ready text.
- Improve finance report.
- Improve donation report.
- Print-friendly report page if easy.

### Deliverables

- Announcement management.
- Basic reporting.

### Definition of Done

- Published announcement can be identified.
- Reports usable by DKM.

---

## Sprint 13 — MVP Hardening

### Goal

Menstabilkan MVP sebelum lanjut public portal.

### Tasks

- Fix bugs from testing.
- Validate permission server-side.
- Improve empty/loading states.
- Improve responsive UI.
- Check finance accuracy.
- Check donation linkage.
- Build/lint/test.

### Deliverables

- Stable MVP.

### Definition of Done

- Acceptance test MVP passed.
- No critical security/data bugs.

---

## Sprint 14 — Public Portal Foundation

### Goal

Membuat halaman publik masjid.

### Tasks

- Public layout.
- Home page.
- Profil masjid.
- Jadwal publik.
- Event published list.
- Announcement public list.
- Contact section.

### Deliverables

- Public portal v1.

### Definition of Done

- Public can access safe pages without login.
- No private data exposed.

---

## Sprint 15 — Public Donation & Transparency

### Goal

Jamaah bisa melihat info donasi dan laporan publik agregat.

### Tasks

- Public donation page.
- Donation instruction/settings.
- Public financial summary.
- Aggregate income/expense by category.
- Hide sensitive details.

### Deliverables

- Public transparency v1.

### Definition of Done

- Public report aggregate only.
- Donor/private finance data hidden.

---

## Sprint 16 — Settings Module

### Goal

Admin bisa mengatur profil masjid dan public portal.

### Tasks

- Mosque profile settings.
- Donation account settings.
- Public portal visibility settings.
- Report settings.

### Deliverables

- Settings management.

### Definition of Done

- Public portal uses settings data.
- Only authorized users can edit settings.

---

## Sprint 17 — Zakat Module

### Goal

Manajemen zakat manual.

### Tasks

- Zakat dashboard.
- Input zakat.
- Muzakki record.
- Mustahik record.
- Distribution record.
- Zakat report.

### Deliverables

- Zakat management.

### Definition of Done

- Zakat collected/distributed can be tracked.
- Mustahik data protected.

---

## Sprint 18 — Wakaf Module

### Goal

Manajemen wakaf uang/barang.

### Tasks

- Wakaf list.
- Input wakaf.
- Wakif data.
- Purpose/status tracking.
- Wakaf report.

### Deliverables

- Wakaf management.

### Definition of Done

- Wakaf records manageable.
- Public display only safe aggregate/progress.

---

## Sprint 19 — Qurban Module Part 1

### Goal

Pendaftaran dan pembayaran qurban.

### Tasks

- Participant list.
- Animal type selection.
- Sapi share support.
- Group code.
- Payment status.
- Link payment to finance if needed.

### Deliverables

- Qurban participant/payment tracking.

### Definition of Done

- Sapi 1/7 logic works.
- Payment tracking works.

---

## Sprint 20 — Qurban Module Part 2

### Goal

Hewan, penyembelihan, distribusi, kupon.

### Tasks

- Animal list.
- Assign animal to group.
- Slaughter status.
- Distribution status.
- Coupon code.
- Print coupon.
- Qurban report.

### Deliverables

- Full qurban workflow.

### Definition of Done

- Qurban flow end-to-end usable.

---

## Sprint 21 — Export, Audit Log, Production Readiness

### Goal

Menjadikan sistem lebih siap produksi.

### Tasks

- Export finance report.
- Export donation report.
- Audit log important actions.
- Improve tests.
- Security review.
- Final UI polish.

### Deliverables

- Production-ready pass.

### Definition of Done

- Critical workflows tested.
- Sensitive data protected.
- Build passes.
