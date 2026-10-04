# MasjidOS Guide Pack

Dokumen ini adalah pusat arahan untuk AI coding agent agar pengembangan **MasjidOS / Sistem Manajemen Masjid** tetap konsisten, aman, dan tidak melebar tanpa kontrol.

## Cara Pakai untuk AI Agent

Berikan instruksi ini ke Codex/Hermes/Kilo sebelum mulai coding:

```text
You must read and obey all markdown guide files in this project before planning or coding.
Treat these files as the source of truth for product scope, architecture, UI/UX, database, roles, sprint plan, and acceptance criteria.
If there is a conflict between existing code and these guides, explain the conflict first and propose the safest resolution before changing code.
Do not implement features outside the current sprint unless explicitly approved.
```

## Urutan Baca yang Disarankan

1. `01_PRODUCT_SPEC.md`
2. `02_MODULE_SCOPE.md`
3. `03_ARCHITECTURE_GUIDE.md`
4. `04_DATABASE_GUIDE.md`
5. `05_ROLE_PERMISSION_GUIDE.md`
6. `06_UI_UX_SHADCN_GUIDE.md`
7. `07_DEVELOPMENT_RULES.md`
8. `08_AI_AGENT_WORKFLOW.md`
9. `09_SPRINT_ROADMAP.md`
10. `10_KANBAN_BOARD.md`
11. `11_ACCEPTANCE_TESTING.md`
12. `12_AGENT_PROMPTS.md`

## Prinsip Utama

- Jangan langsung build semua fitur.
- Audit repo dulu sebelum coding.
- Kerjakan per sprint.
- Finance dan donation harus akurat dan auditable.
- Permission harus dicek di server, bukan hanya frontend.
- UI harus konsisten dengan ShadCN Vue.
- Fitur besar wajib dipecah menjadi task kecil.
- Setelah setiap sprint, update kanban board.

## Definition of Done Global

Sebuah fitur dianggap selesai jika:

- CRUD utama berjalan.
- Validasi server-side ada.
- Role/permission sesuai.
- UI responsive dan konsisten.
- Empty state tersedia.
- Error state jelas.
- Data penting tidak bocor ke public.
- Build/lint/test tidak rusak.
- Dokumentasi atau catatan perubahan diperbarui.
