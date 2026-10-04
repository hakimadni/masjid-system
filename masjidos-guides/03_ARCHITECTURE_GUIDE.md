# Architecture Guide

## Prinsip Arsitektur

MasjidOS harus dibangun secara modular, incremental, dan maintainable.

AI agent wajib:

- Mengikuti struktur existing project.
- Tidak rewrite total tanpa alasan kuat.
- Memisahkan business logic dari UI.
- Menjaga finance logic tetap auditable.
- Membuat perubahan kecil per sprint.
- Menjelaskan risiko sebelum migrasi besar.

## Layer yang Disarankan

Jika framework mendukung, gunakan pola berikut:

```text
Routes
Controllers / Handlers
Request Validation
Services
Models / Entities
Database / Migrations
Frontend Pages / Components
```

## Service Layer

Gunakan service untuk logic yang tidak trivial.

### FinanceService

Tanggung jawab:

- Membuat transaksi
- Validasi category type vs transaction type
- Approve transaksi
- Reject transaksi
- Hitung saldo resmi
- Hitung laporan date range

### DonationService

Tanggung jawab:

- Membuat donasi
- Confirm donasi
- Reject donasi
- Membuat/menghubungkan transaksi income
- Menjaga anonymous donor rule

### DashboardService

Tanggung jawab:

- Summary cards
- Latest transactions
- Latest donations
- Upcoming schedules
- Upcoming events

### ReportService

Tanggung jawab:

- Financial report
- Donation report
- Asset report
- Public report aggregate

## Money Handling

Aturan wajib:

- Jangan pakai floating point untuk money.
- Gunakan decimal fixed precision atau integer minor unit.
- Format output sebagai Rupiah di frontend.
- Validasi amount > 0.

## Date Handling

Aturan:

- Gunakan date untuk transaction_date.
- Gunakan datetime untuk event start/end.
- Gunakan timezone app secara konsisten.
- Filter bulanan harus jelas start-of-month sampai end-of-month.

## Approval Workflow

Transaksi keuangan memiliki status:

- draft
- pending
- approved
- rejected

Hanya `approved` yang masuk saldo resmi.

Rule:

- Normal user tidak boleh menghapus approved transaction.
- Approved transaction harus punya `approved_by` dan `approved_at`.
- Rejected transaction sebaiknya punya `rejected_reason`.

## Public vs Admin Separation

Admin route harus protected auth.

Public portal hanya boleh expose:

- Profil masjid
- Jadwal publik
- Event published
- Pengumuman published public
- Ringkasan keuangan agregat
- Info donasi umum

Public portal tidak boleh expose:

- Bukti transaksi
- Catatan internal finance
- Data mustahik
- Data pribadi donor
- Dokumen internal
- User/admin data

## File Upload

Jika upload digunakan:

- Validasi MIME type.
- Validasi ukuran file.
- Simpan path secara aman.
- Jangan expose file private langsung ke public.
- Proof transaksi hanya bisa dilihat role authorized.

## Error Handling

Minimal:

- Validation error jelas.
- Unauthorized error tidak membuka data.
- Failed upload ditangani.
- Failed approval/reject ditangani.

## Performance

Untuk MVP, cukup:

- Pagination untuk list besar.
- Filter query server-side.
- Hindari N+1 query.
- Index field penting: date, status, type, category_id.

## Logging & Audit

Fase lanjutan:

- Audit log untuk transaksi penting.
- Log approval finance.
- Log donation confirmation.
- Log perubahan role/permission.
