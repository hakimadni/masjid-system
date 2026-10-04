# Role & Permission Guide

## Prinsip

- Permission harus ditegakkan di server-side.
- Frontend role-based navigation hanya untuk UX.
- Super Admin tidak boleh terkunci dari sistem.
- Data sensitif seperti mustahik, donor private, dan finance proof harus dibatasi.

## Roles

| Role | Slug | Deskripsi |
|---|---|---|
| Super Admin | super_admin | Akses penuh |
| Ketua DKM | ketua_dkm | Monitoring, approval, laporan |
| Bendahara | bendahara | Keuangan dan donasi |
| Sekretaris | sekretaris | Kegiatan, dokumen, pengumuman |
| Panitia | panitia | Event tertentu |
| Marbot | marbot | Operasional harian dan aset terbatas |
| Jamaah | jamaah | Akses publik/terbatas |
| Donatur | donatur | Riwayat donasi pribadi fase lanjutan |

## Permission List

### Dashboard

- `dashboard.view`

### Finance

- `finance.view`
- `finance.create`
- `finance.update`
- `finance.delete`
- `finance.approve`
- `finance.reject`
- `finance.report`
- `finance.proof.view`

### Donation

- `donation.view`
- `donation.create`
- `donation.update`
- `donation.delete`
- `donation.confirm`
- `donation.reject`
- `donation.report`

### Schedule

- `schedule.view`
- `schedule.manage`

### Event

- `event.view`
- `event.manage`
- `event.report`

### Jamaah

- `jamaah.view`
- `jamaah.manage`
- `jamaah.sensitive.view`

### Asset

- `asset.view`
- `asset.manage`

### Document

- `document.view`
- `document.manage`
- `document.private.view`

### Announcement

- `announcement.view`
- `announcement.manage`

### Report

- `report.view`
- `report.export`
- `report.public.manage`

### Settings

- `setting.view`
- `setting.manage`

### Public Portal

- `public.manage`

### Later Modules

- `zakat.view`
- `zakat.manage`
- `wakaf.view`
- `wakaf.manage`
- `qurban.view`
- `qurban.manage`
- `audit_log.view`

## Default Role Permission Matrix

| Permission | Super Admin | Ketua DKM | Bendahara | Sekretaris | Panitia | Marbot |
|---|---:|---:|---:|---:|---:|---:|
| dashboard.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| finance.view | ✅ | ✅ | ✅ | ❌ | limited | ❌ |
| finance.create | ✅ | ❌ | ✅ | ❌ | limited | ❌ |
| finance.update | ✅ | ❌ | ✅ | ❌ | limited | ❌ |
| finance.delete | ✅ | ❌ | limited | ❌ | ❌ | ❌ |
| finance.approve | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| finance.reject | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| finance.report | ✅ | ✅ | ✅ | ❌ | limited | ❌ |
| finance.proof.view | ✅ | ✅ | ✅ | ❌ | limited | ❌ |
| donation.view | ✅ | ✅ | ✅ | ❌ | limited | ❌ |
| donation.create | ✅ | ✅ | ✅ | ❌ | limited | ❌ |
| donation.update | ✅ | ❌ | ✅ | ❌ | limited | ❌ |
| donation.confirm | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| donation.reject | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| schedule.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| schedule.manage | ✅ | ✅ | ❌ | ✅ | limited | limited |
| event.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| event.manage | ✅ | ✅ | ❌ | ✅ | limited | ❌ |
| jamaah.view | ✅ | ✅ | ❌ | ✅ | limited | ❌ |
| jamaah.manage | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ |
| jamaah.sensitive.view | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| asset.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| asset.manage | ✅ | ✅ | ❌ | ✅ | ❌ | limited |
| document.view | ✅ | ✅ | ✅ | ✅ | limited | ❌ |
| document.manage | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ |
| announcement.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| announcement.manage | ✅ | ✅ | ❌ | ✅ | limited | ❌ |
| report.view | ✅ | ✅ | ✅ | ✅ | limited | ❌ |
| report.export | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| setting.manage | ✅ | limited | ❌ | ❌ | ❌ | ❌ |

`limited` berarti hanya boleh jika fitur mendukung scope terbatas, misalnya event yang ditugaskan.

## Route Protection Rules

- Semua `/admin/*` wajib auth.
- Semua finance mutation wajib permission.
- Semua approval/reject wajib permission khusus.
- Public route tidak boleh load admin-only relations.

## Sensitive Data Rules

### Mustahik

Data mustahik hanya bisa dilihat oleh:

- Super Admin
- Ketua DKM
- Role yang diberi `jamaah.sensitive.view`

### Donor Private Data

Di public report:

- Jika `is_anonymous = true`, tampilkan sebagai `Hamba Allah`.
- Jangan tampilkan nomor HP/email.

### Finance Proof

Bukti transaksi hanya bisa dilihat role dengan:

- `finance.proof.view`

## UI Navigation Visibility

Sidebar menu hanya muncul jika user punya permission minimal:

- Dashboard: `dashboard.view`
- Keuangan: `finance.view`
- Donasi: `donation.view`
- Jadwal: `schedule.view`
- Kegiatan: `event.view`
- Jamaah: `jamaah.view`
- Inventaris: `asset.view`
- Dokumen: `document.view`
- Pengumuman: `announcement.view`
- Laporan: `report.view`
- Settings: `setting.view` atau `setting.manage`
