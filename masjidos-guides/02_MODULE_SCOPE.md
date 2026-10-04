# Module Scope Guide

Dokumen ini mendefinisikan cakupan fitur per modul. AI agent tidak boleh menambahkan fitur besar di luar scope sprint tanpa approval.

## 1. Dashboard

### MVP

- Saldo kas resmi
- Pemasukan bulan ini
- Pengeluaran bulan ini
- Donasi bulan ini
- Transaksi pending
- Jadwal terdekat
- Kegiatan terdekat
- Pengumuman aktif
- Transaksi terbaru
- Donasi terbaru

### Later

- Chart pemasukan vs pengeluaran
- Chart donasi per kategori
- Widget aset bermasalah
- Shortcut action berdasarkan role

## 2. Keuangan Masjid

### MVP

- Kategori pemasukan/pengeluaran
- Input pemasukan
- Input pengeluaran
- Status draft/pending/approved/rejected
- Approval oleh role tertentu
- Filter tanggal, kategori, status, tipe
- Detail transaksi
- Upload bukti jika sistem upload tersedia
- Ringkasan laporan

### Later

- Export Excel/PDF
- Audit log
- Saldo awal per periode
- Rekonsiliasi bank
- Multi rekening kas

## 3. Donasi & Donatur

### MVP

- Input donasi manual
- Kategori donasi
- Donatur nama/phone/email optional
- Anonymous flag
- Status pending/confirmed/rejected
- Confirmed donation dapat membuat transaksi income
- Summary donasi

### Later

- Link donasi publik
- Form konfirmasi donasi publik
- Riwayat donatur
- Kwitansi otomatis
- Payment gateway/QRIS API

## 4. Jadwal

### MVP

- Jadwal imam
- Jadwal muadzin
- Jadwal khatib
- Jadwal bilal
- Jadwal kultum
- Jadwal kajian
- Jadwal marbot
- Filter tipe dan tanggal

### Later

- Calendar view
- Reminder
- Recurring schedule
- Public display

## 5. Kegiatan Masjid

### MVP

- CRUD kegiatan
- Tipe kegiatan
- Pemateri
- Lokasi
- Waktu mulai/selesai
- Poster optional
- Estimasi budget
- Status draft/published/completed/cancelled

### Later

- Registrasi peserta
- Absensi peserta
- Laporan kegiatan
- LPJ kegiatan
- Dokumentasi kegiatan

## 6. Jamaah

### MVP

- CRUD jamaah
- Kategori jamaah: umum, pengurus, relawan, mustahik, donatur
- Data kontak dan alamat dasar
- Active/inactive
- Privacy untuk mustahik

### Later

- Riwayat partisipasi kegiatan
- Relawan skill mapping
- Import Excel
- Segment broadcast

## 7. Inventaris & Aset

### MVP

- CRUD aset
- Kategori
- Jumlah
- Kondisi
- Lokasi
- Harga/tanggal beli optional
- Foto optional

### Later

- Peminjaman aset
- Maintenance log
- QR code label aset
- Depresiasi sederhana

## 8. Dokumen

### MVP

- Upload dokumen
- Tipe dokumen
- Nomor dokumen
- Tanggal dokumen
- Deskripsi

### Later

- Auto nomor surat
- Template surat
- Generate PDF surat
- Approval dokumen

## 9. Pengumuman

### MVP

- CRUD pengumuman
- Status draft/published/archived
- Public/private flag
- Copy text untuk WhatsApp

### Later

- Broadcast helper template
- Jadwal publikasi
- Target audience

## 10. Laporan

### MVP

- Laporan keuangan dasar
- Laporan donasi dasar
- Laporan aset dasar
- Filter date range

### Later

- Export Excel/PDF
- Public transparency report
- Report template branding masjid

## 11. Public Portal

### Later Phase

- Home
- Profil masjid
- Jadwal publik
- Kegiatan/kajian
- Pengumuman
- Donasi
- Laporan publik agregat
- Kontak

## 12. Zakat

### Later Phase

- Muzakki
- Mustahik
- Zakat fitrah
- Zakat mal manual
- Distribusi
- Laporan

## 13. Wakaf

### Later Phase

- Wakif
- Wakaf uang/barang
- Peruntukan
- Status penggunaan
- Laporan

## 14. Qurban

### Later Phase

- Peserta qurban
- Kambing/sapi
- Sapi 1/7 share
- Kelompok sapi
- Hewan qurban
- Pembayaran
- Penyembelihan
- Distribusi
- Kupon
- Laporan
