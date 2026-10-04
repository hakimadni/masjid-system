<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Asset;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Event;
use App\Models\FinanceAccount;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Models\Mosque;
use App\Models\PrayerSchedule;
use App\Models\ServiceSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminMvpSeeder extends Seeder
{
    private const DEFAULT_MOSQUE_SLUG = 'default-masjid';

    public function run(): void
    {
        $mosqueId = Mosque::query()->firstOrCreate(
            ['slug' => self::DEFAULT_MOSQUE_SLUG],
            ['name' => 'Masjid Utama', 'is_active' => true]
        )->id;

        FinanceAccount::query()->firstOrCreate(
            ['mosque_id' => $mosqueId, 'name' => 'Kas Utama'],
            ['code' => 'KAS-001', 'account_type' => 'cash', 'opening_balance' => 0, 'is_active' => true]
        );

        FinanceAccount::query()->firstOrCreate(
            ['mosque_id' => $mosqueId, 'name' => 'Rekening Operasional'],
            ['code' => 'BANK-001', 'account_type' => 'bank', 'opening_balance' => 0, 'is_active' => true]
        );

        foreach ([
            ['name' => 'Infaq Harian', 'entry_type' => 'income'],
            ['name' => 'Donasi Program', 'entry_type' => 'income'],
            ['name' => 'Operasional Masjid', 'entry_type' => 'expense'],
            ['name' => 'Perawatan Fasilitas', 'entry_type' => 'expense'],
        ] as $category) {
            FinanceCategory::query()->firstOrCreate(
                ['mosque_id' => $mosqueId, 'name' => $category['name']],
                ['entry_type' => $category['entry_type'], 'is_active' => true]
            );
        }

        $accountId = FinanceAccount::query()->where('mosque_id', $mosqueId)->where('name', 'Kas Utama')->value('id');
        $incomeCategoryId = FinanceCategory::query()->where('mosque_id', $mosqueId)->where('name', 'Infaq Harian')->value('id');
        $expenseCategoryId = FinanceCategory::query()->where('mosque_id', $mosqueId)->where('name', 'Operasional Masjid')->value('id');
        $adminUserId = User::query()->where('email', 'admin@masjid.local')->value('id');

        FinanceTransaction::query()->firstOrCreate(
            ['mosque_id' => $mosqueId, 'reference_no' => 'FIN-SEED-001'],
            [
                'finance_account_id' => $accountId,
                'finance_category_id' => $incomeCategoryId,
                'entry_type' => 'income',
                'title' => 'Infaq Harian Pekanan',
                'transaction_date' => now()->toDateString(),
                'amount' => 2500000,
                'status' => 'approved',
                'payment_method' => 'cash',
                'notes' => 'Seed dashboard pemasukan.',
                'created_by' => $adminUserId,
                'approved_by' => $adminUserId,
                'approved_at' => now(),
            ]
        );

        FinanceTransaction::query()->firstOrCreate(
            ['mosque_id' => $mosqueId, 'reference_no' => 'FIN-SEED-002'],
            [
                'finance_account_id' => $accountId,
                'finance_category_id' => $expenseCategoryId,
                'entry_type' => 'expense',
                'title' => 'Pembelian Alat Kebersihan',
                'transaction_date' => now()->toDateString(),
                'amount' => 450000,
                'status' => 'pending',
                'payment_method' => 'transfer',
                'notes' => 'Seed dashboard pengeluaran.',
                'created_by' => $adminUserId,
            ]
        );

        $donor = Donor::query()->firstOrCreate(
            ['mosque_id' => $mosqueId, 'name' => 'Donatur Tetap'],
            ['phone' => '081234000111']
        );

        Donation::query()->firstOrCreate(
            ['mosque_id' => $mosqueId, 'campaign' => 'Program Ramadhan', 'donor_id' => $donor->id],
            [
                'donation_date' => now()->toDateString(),
                'amount' => 1000000,
                'method' => 'transfer',
                'status' => 'confirmed',
                'notes' => 'Seed dashboard donasi.',
                'created_by' => $adminUserId,
                'confirmed_by' => $adminUserId,
                'confirmed_at' => now(),
            ]
        );

        Event::query()->firstOrCreate(
            ['mosque_id' => $mosqueId, 'title' => 'Kajian Ahad Pagi'],
            [
                'start_at' => now()->addDays(3)->setTime(8, 0),
                'end_at' => now()->addDays(3)->setTime(10, 0),
                'location' => 'Aula Utama',
                'pic_name' => 'Sekretaris DKM',
                'status' => 'published',
                'notes' => 'Kajian rutin untuk jamaah umum.',
            ]
        );

        Announcement::query()->firstOrCreate(
            ['mosque_id' => $mosqueId, 'title' => 'Kerja Bakti Pekanan'],
            [
                'content' => 'Pengurus dan jamaah diundang ikut kerja bakti setiap Sabtu pagi.',
                'published_at' => now(),
                'status' => 'published',
            ]
        );

        Asset::query()->firstOrCreate(
            ['mosque_id' => $mosqueId, 'name' => 'Karpet Utama'],
            [
                'category' => 'Perlengkapan Ibadah',
                'location' => 'Ruang Shalat Utama',
                'quantity' => 12,
                'condition' => 'baik',
                'status' => 'active',
                'notes' => 'Dibersihkan setiap pekan.',
            ]
        );

        PrayerSchedule::query()->firstOrCreate(
            ['mosque_id' => $mosqueId, 'schedule_date' => now()->toDateString(), 'prayer_name' => 'jumat'],
            [
                'prayer_time' => '12:00',
                'imam_name' => 'Ustadz Ahmad',
                'muadzin_name' => 'Bpk. Ridwan',
                'khatib_name' => 'KH. Maulana',
                'status' => 'published',
            ]
        );

        ServiceSchedule::query()->firstOrCreate(
            ['mosque_id' => $mosqueId, 'title' => 'Petugas Kajian Malam Jumat'],
            [
                'role_type' => 'petugas',
                'person_name' => 'Tim Sekretariat',
                'location' => 'Serambi Masjid',
                'scheduled_at' => now()->addDays(1)->setTime(19, 0),
                'status' => 'published',
                'notes' => 'Persiapan sound system dan konsumsi.',
            ]
        );
    }
}
