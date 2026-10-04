# Acceptance Testing Guide

Dokumen ini berisi checklist untuk memastikan fitur benar-benar selesai.

## Global Acceptance Criteria

Setiap fitur harus memenuhi:

- [ ] Route/page bisa dibuka oleh role authorized.
- [ ] Route/page ditolak untuk role unauthorized.
- [ ] Create works.
- [ ] Edit works jika fitur mendukung.
- [ ] Delete works jika allowed.
- [ ] Validation error muncul jelas.
- [ ] Empty state muncul saat data kosong.
- [ ] Status badge tampil konsisten.
- [ ] Mobile layout tidak rusak.
- [ ] Build/lint/test tidak rusak.

## Finance Acceptance Test

### Create Income

- [ ] Bendahara bisa buat income.
- [ ] Amount wajib > 0.
- [ ] Category wajib tipe income.
- [ ] Status default sesuai workflow: draft/pending.
- [ ] Income pending belum masuk saldo resmi.

### Create Expense

- [ ] Bendahara bisa buat expense.
- [ ] Amount wajib > 0.
- [ ] Category wajib tipe expense.
- [ ] Expense pending belum mengurangi saldo resmi.

### Approval

- [ ] Ketua DKM bisa approve.
- [ ] Super Admin bisa approve.
- [ ] Bendahara tidak bisa approve jika rule melarang.
- [ ] Approved transaction masuk saldo resmi.
- [ ] approved_by terisi.
- [ ] approved_at terisi.

### Rejection

- [ ] Ketua DKM bisa reject.
- [ ] Rejected transaction tidak masuk saldo resmi.
- [ ] rejected_reason tersimpan jika field tersedia.

### Delete Restriction

- [ ] Draft/pending bisa dihapus oleh authorized role.
- [ ] Approved tidak bisa dihapus oleh normal user.
- [ ] Super Admin behavior sesuai rule project.

## Donation Acceptance Test

### Create Donation

- [ ] Admin/Bendahara bisa input donasi manual.
- [ ] Amount wajib > 0.
- [ ] Category wajib.
- [ ] Anonymous flag bisa dipilih.

### Confirm Donation

- [ ] Confirmed donation status berubah confirmed.
- [ ] confirmed_by terisi.
- [ ] confirmed_at terisi.
- [ ] Linked financial income dibuat atau tersambung.
- [ ] Income dari donation mengikuti finance approval/confirmed rule yang dipilih.

### Reject Donation

- [ ] Rejected donation tidak membuat finance income.
- [ ] Rejected donation tidak muncul sebagai confirmed donation.

### Anonymous Rule

- [ ] Public display menampilkan anonymous sebagai Hamba Allah.
- [ ] Phone/email tidak tampil di public.

## Dashboard Acceptance Test

- [ ] Saldo Kas hanya dari approved transactions.
- [ ] Pemasukan Bulan Ini benar.
- [ ] Pengeluaran Bulan Ini benar.
- [ ] Donasi Bulan Ini benar.
- [ ] Pending approval count benar.
- [ ] Jadwal terdekat sorted by date/time.
- [ ] Kegiatan terdekat sorted by datetime.
- [ ] Latest transactions sorted newest.
- [ ] Latest donations sorted newest.

## Schedule Acceptance Test

- [ ] Bisa create jadwal imam.
- [ ] Bisa create jadwal muadzin.
- [ ] Bisa create jadwal khatib.
- [ ] Bisa create jadwal kajian.
- [ ] Filter type bekerja.
- [ ] Filter date bekerja.
- [ ] Unauthorized user tidak bisa manage.

## Event Acceptance Test

- [ ] Bisa create event.
- [ ] Bisa edit event.
- [ ] Status draft/published/completed/cancelled bekerja.
- [ ] Published event muncul di public later.
- [ ] Cancelled event tidak muncul sebagai upcoming utama.

## Jamaah Acceptance Test

- [ ] Bisa create jamaah.
- [ ] Bisa edit jamaah.
- [ ] Bisa set category.
- [ ] Mustahik data protected.
- [ ] Unauthorized role tidak bisa lihat sensitive notes.

## Asset Acceptance Test

- [ ] Bisa create asset.
- [ ] Quantity minimal 1.
- [ ] Condition badge tampil.
- [ ] Asset bermasalah terhitung.

## Document Acceptance Test

- [ ] Bisa upload/create document.
- [ ] Document type filter bekerja.
- [ ] Private document tidak bisa diakses public.
- [ ] Unauthorized role tidak bisa download private file.

## Announcement Acceptance Test

- [ ] Bisa create announcement.
- [ ] Bisa publish/archive.
- [ ] Public/private flag bekerja.
- [ ] Copy WhatsApp text tersedia jika implemented.

## Public Portal Acceptance Test

- [ ] Public home bisa diakses tanpa login.
- [ ] Profil masjid tampil.
- [ ] Published events tampil.
- [ ] Public announcements tampil.
- [ ] Public financial summary hanya aggregate.
- [ ] Tidak ada proof transaksi.
- [ ] Tidak ada private donor data.
- [ ] Tidak ada mustahik data.

## Zakat Acceptance Test

- [ ] Bisa input zakat fitrah.
- [ ] Bisa input zakat mal manual.
- [ ] Bisa input mustahik.
- [ ] Mustahik protected.
- [ ] Distribusi tercatat.
- [ ] Report menampilkan collected vs distributed.

## Wakaf Acceptance Test

- [ ] Bisa input wakaf uang.
- [ ] Bisa input wakaf barang.
- [ ] Status wakaf bisa dilacak.
- [ ] Report wakaf tampil.

## Qurban Acceptance Test

- [ ] Bisa input peserta kambing.
- [ ] Bisa input peserta sapi.
- [ ] Sapi max 7 share.
- [ ] Payment status works.
- [ ] Animal assignment works.
- [ ] Slaughter status works.
- [ ] Distribution status works.
- [ ] Kupon printable.

## Security Test

- [ ] Admin route requires login.
- [ ] Permission checked server-side.
- [ ] Public route does not expose private relations.
- [ ] File upload validates type and size.
- [ ] Normal user cannot mutate finance approval.
- [ ] Normal user cannot view finance proof.

## Production Readiness Test

- [ ] Migration fresh works on local/dev.
- [ ] Seeder works idempotently.
- [ ] Build passes.
- [ ] Lint passes if available.
- [ ] Tests pass if available.
- [ ] No console errors on main pages.
- [ ] No broken links in sidebar.
- [ ] No debug text visible.
