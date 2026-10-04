# UI/UX Guide — ShadCN Vue

## Direction

MasjidOS harus terasa seperti modern SaaS admin dashboard yang:

- Clean
- Calm
- Professional
- Trustworthy
- Cocok untuk operasional DKM/masjid
- Tidak terlalu flashy
- Cepat dipakai untuk pekerjaan harian
- Responsive untuk desktop, tablet, dan mobile

## Design Foundation

Gunakan ShadCN Vue sebagai primary UI foundation.

Komponen yang diprioritaskan:

- Button
- Card
- Table
- Badge
- Dialog
- Sheet
- DropdownMenu
- Select
- Input
- Textarea
- Checkbox
- Tabs
- Separator
- Calendar / DatePicker
- Popover
- Command
- Toast / Sonner
- Skeleton
- Alert
- Tooltip
- Breadcrumb
- Avatar
- Sidebar jika tersedia

## Color Direction

- Base: neutral, clean background.
- Accent: soft green.
- Hindari warna terlalu neon.
- Gunakan border subtle.
- Gunakan shadow minimal.

## Layout Admin

### Sidebar

Menu utama:

- Dashboard
- Keuangan
- Donasi
- Jadwal
- Kegiatan
- Jamaah
- Inventaris
- Dokumen
- Pengumuman
- Laporan
- Settings

Later:

- Zakat
- Wakaf
- Qurban
- Portal Publik
- Audit Log

Rules:

- Sidebar desktop selalu terlihat.
- Mobile sidebar menjadi Sheet/drawer.
- Menu aktif harus jelas.
- Menu visibility berdasarkan permission.

### Topbar

Berisi:

- Page title
- Breadcrumb jika berguna
- User menu
- Optional mosque selector
- Optional quick action button

## Dashboard UI

Gunakan summary cards untuk:

- Saldo Kas
- Pemasukan Bulan Ini
- Pengeluaran Bulan Ini
- Donasi Bulan Ini
- Transaksi Pending
- Kegiatan Terdekat

Gunakan card sections untuk:

- Transaksi Terbaru
- Donasi Terbaru
- Jadwal Terdekat
- Pengumuman Aktif

Jika chart library sudah ada, boleh gunakan chart. Jika belum ada, jangan tambah dependency berat untuk MVP.

## Table Pattern

Setiap list page sebaiknya punya:

- PageHeader
- Primary action button
- Search input jika relevan
- Date range filter jika relevan
- Category/status filter jika relevan
- Table
- Badge status
- Row action dropdown
- Pagination
- Empty state
- Loading skeleton jika async

Row action dropdown:

- Detail
- Edit
- Approve/Confirm jika authorized
- Reject jika authorized
- Delete hanya jika allowed

## Form Pattern

Gunakan form dalam Card.

Pattern:

- Page title
- Back button
- Form sections
- Clear labels
- Validation message dekat field
- Submit button dengan loading state
- Cancel/back button

Field mapping:

- Enum/status/category pakai Select.
- Date pakai DatePicker jika tersedia.
- Long text pakai Textarea.
- Boolean pakai Checkbox/Switch jika tersedia.
- Money pakai Input dengan formatting/parsing jelas.

## Status Badge

| Status | Color Direction |
|---|---|
| approved | green |
| confirmed | green |
| published | green |
| pending | yellow/muted |
| draft | gray/muted |
| rejected | red |
| cancelled | red |
| completed | blue |
| archived | gray |

## Indonesian UI Labels

Gunakan label Indonesia.

Contoh:

- Tambah Transaksi
- Pemasukan
- Pengeluaran
- Saldo Kas
- Setujui
- Tolak
- Konfirmasi
- Batalkan
- Simpan
- Hapus
- Detail
- Ubah
- Filter
- Tanggal Mulai
- Tanggal Selesai
- Kategori
- Status
- Metode Pembayaran
- Bukti Transaksi
- Tidak ada data

## Empty State Examples

Finance:

> Belum ada transaksi. Tambahkan pemasukan atau pengeluaran pertama untuk mulai mencatat keuangan masjid.

Donation:

> Belum ada donasi. Donasi manual dapat dicatat oleh bendahara atau admin.

Schedule:

> Belum ada jadwal. Tambahkan jadwal imam, muadzin, khatib, atau kajian.

## Mobile Requirements

- Table jangan overflow buruk.
- Gunakan horizontal scroll atau card list pada mobile.
- Sidebar jadi drawer.
- Primary action tetap mudah diakses.
- Filter bisa diletakkan dalam collapsible section.

## Production Feel Checklist

- Tidak ada page kosong tanpa empty state.
- Tidak ada button tanpa loading feedback pada submit.
- Tidak ada status plain text jika bisa pakai Badge.
- Tidak ada destructive action tanpa confirm dialog.
- Tidak ada form panjang tanpa sectioning.
- Tidak ada label campur English/Indonesia tanpa alasan.
