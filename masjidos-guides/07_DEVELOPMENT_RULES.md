# Development Rules

Dokumen ini berisi aturan kerja yang harus dipatuhi AI agent saat mengembangkan MasjidOS.

## General Rules

1. Audit repo sebelum coding.
2. Jangan rewrite total tanpa approval.
3. Jangan hapus fitur existing tanpa alasan dan approval.
4. Ikuti convention project.
5. Kerjakan per sprint/task kecil.
6. Setelah task selesai, update kanban board.
7. Setelah sprint selesai, berikan summary perubahan.
8. Jangan implement fitur di luar sprint aktif kecuali bug kecil yang jelas terkait.

## Coding Rules

- Gunakan nama yang jelas.
- Hindari duplicate logic.
- Business logic jangan ditaruh di UI.
- Validasi server-side wajib.
- Authorization server-side wajib.
- Gunakan service untuk logic finance/donation/report.
- Jangan hardcode role di banyak tempat jika bisa dibuat helper/policy.
- Jangan expose private data di response public.

## Database Rules

- Migration harus additive dan aman.
- Jangan drop table/column tanpa approval.
- Seed default role/category harus idempotent.
- Foreign key gunakan sesuai convention.
- Tambahkan index untuk query sering dipakai.
- Jangan pakai floating point untuk uang.

## Finance Rules

- Hanya approved transaction yang mempengaruhi saldo resmi.
- Amount harus > 0.
- Category type harus cocok dengan transaction type.
- Normal user tidak boleh delete approved transaction.
- Approval harus mencatat approved_by dan approved_at.
- Rejection sebaiknya mencatat rejected_reason.
- Bukti transaksi hanya untuk authorized users.

## Donation Rules

- Donation status dikontrol server-side.
- Confirmed donation dapat membuat linked financial income transaction.
- Rejected donation tidak boleh membuat income.
- Anonymous donor tampil sebagai `Hamba Allah` di public.
- Donor phone/email tidak muncul di public.

## Security Rules

- Admin routes wajib auth.
- Permission checked on server.
- Jangan percaya frontend role checks.
- File upload validate MIME dan size.
- Jangan tampilkan stack trace di UI.
- Public API/page hanya expose safe data.

## UI Rules

- Gunakan ShadCN Vue jika tersedia.
- Gunakan Indonesian labels.
- Gunakan Card untuk section.
- Gunakan Badge untuk status.
- Gunakan Dialog untuk confirm destructive actions.
- Gunakan Skeleton/Loading state untuk async.
- Gunakan Empty state.
- UI harus responsive.

## Git / Change Management Rules

Jika AI agent bisa membuat commit, gunakan commit message jelas:

```text
feat(finance): add transaction approval workflow
fix(donation): prevent rejected donation from creating income
ui(dashboard): add monthly finance summary cards
```

Jika tidak commit, minimal output:

- Files created
- Files modified
- Migration added
- Commands to run
- Manual checks
- Risks

## Commands Rule

Setelah perubahan, agent harus menyarankan command relevan sesuai stack, misalnya:

- install dependency
- run migration
- run seeder
- run build
- run lint
- run test
- start dev server

Jangan invent command kalau stack belum diaudit.
