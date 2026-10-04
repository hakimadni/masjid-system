<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\DonationCategoryController;
use App\Http\Controllers\Admin\DonationController;
use App\Http\Controllers\Admin\DonorController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FinanceCategoryController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\JamaahController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PlaceholderModuleController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\WakafController;
use App\Http\Controllers\Admin\ZakatController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified'])
    ->group(function (): void {
        Route::get('/', fn () => redirect()->route('dashboard'))->name('home');
    });

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::middleware('permission:finance.view')->group(function (): void {
        Route::get('/keuangan', [FinanceController::class, 'index'])->name('finance.index');
        Route::get('/keuangan/pending', [FinanceController::class, 'pending'])->name('finance.pending');
        Route::get('/keuangan/{financeTransaction}', [FinanceController::class, 'show'])->name('finance.show');
        Route::get('/kategori-keuangan', [FinanceCategoryController::class, 'index'])->name('finance-categories.index');
    });
    Route::middleware('permission:finance.create')->group(function (): void {
        Route::post('/keuangan', [FinanceController::class, 'store'])->name('finance.store');
        Route::post('/kategori-keuangan', [FinanceCategoryController::class, 'store'])->name('finance-categories.store');
    });
    Route::middleware('permission:finance.update')->group(function (): void {
        Route::get('/keuangan/{financeTransaction}/edit', [FinanceController::class, 'edit'])->name('finance.edit');
        Route::put('/keuangan/{financeTransaction}', [FinanceController::class, 'update'])->name('finance.update');
        Route::put('/kategori-keuangan/{financeCategory}', [FinanceCategoryController::class, 'update'])->name('finance-categories.update');
    });
    Route::middleware('permission:finance.approve|finance.reject')->group(function (): void {
        Route::patch('/keuangan/{financeTransaction}/status', [FinanceController::class, 'updateStatus'])->name('finance.update-status');
    });
    Route::middleware('permission:finance.delete')->group(function (): void {
        Route::delete('/keuangan/{financeTransaction}', [FinanceController::class, 'destroy'])->name('finance.destroy');
    });

    Route::middleware('permission:donation.view')->group(function (): void {
        Route::get('/admin/donasi', [DonationController::class, 'index'])->name('donations.index');
        Route::get('/admin/donasi/{donation}', [DonationController::class, 'show'])->name('donations.show');
        Route::get('/admin/donatur', [DonorController::class, 'index'])->name('donors.index');
        Route::get('/admin/kategori-donasi', [DonationCategoryController::class, 'index'])->name('donation-categories.index');
    });
    Route::middleware('permission:donation.create')->group(function (): void {
        Route::post('/admin/donasi', [DonationController::class, 'store'])->name('donations.store');
        Route::post('/admin/kategori-donasi', [DonationCategoryController::class, 'store'])->name('donation-categories.store');
    });
    Route::middleware('permission:donation.update')->group(function (): void {
        Route::put('/admin/kategori-donasi/{donationCategory}', [DonationCategoryController::class, 'update'])->name('donation-categories.update');
    });
    Route::middleware('permission:donation.delete')->group(function (): void {
        Route::delete('/admin/kategori-donasi/{donationCategory}', [DonationCategoryController::class, 'destroy'])->name('donation-categories.destroy');
    });
    Route::middleware('permission:donation.confirm|donation.reject')->group(function (): void {
        Route::patch('/admin/donasi/{donation}/status', [DonationController::class, 'updateStatus'])->name('donations.update-status');
    });

    Route::middleware('permission:schedule.view')->group(function (): void {
        Route::get('/admin/jadwal', [ScheduleController::class, 'index'])->name('schedules.index');
    });
    Route::middleware('permission:schedule.manage')->group(function (): void {
        Route::post('/admin/jadwal/shalat', [ScheduleController::class, 'storePrayer'])->name('schedules.store-prayer');
        Route::patch('/admin/jadwal/shalat/{prayerSchedule}/status', [ScheduleController::class, 'updatePrayerStatus'])->name('schedules.prayer-status');
        Route::delete('/admin/jadwal/shalat/{prayerSchedule}', [ScheduleController::class, 'destroyPrayer'])->name('schedules.destroy-prayer');
        Route::post('/admin/jadwal/petugas', [ScheduleController::class, 'storeService'])->name('schedules.store-service');
        Route::patch('/admin/jadwal/petugas/{serviceSchedule}/status', [ScheduleController::class, 'updateServiceStatus'])->name('schedules.service-status');
        Route::delete('/admin/jadwal/petugas/{serviceSchedule}', [ScheduleController::class, 'destroyService'])->name('schedules.destroy-service');
    });

    Route::middleware('permission:event.view')->group(function (): void {
        Route::get('/event', [EventController::class, 'index'])->name('events.index');
        Route::get('/event/{event}', [EventController::class, 'show'])->name('events.show');
    });
    Route::middleware('permission:event.manage')->group(function (): void {
        Route::post('/event', [EventController::class, 'store'])->name('events.store');
        Route::patch('/event/{event}/status', [EventController::class, 'updateStatus'])->name('events.update-status');
        Route::delete('/event/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    });

    Route::middleware('permission:jamaah.view')->group(function (): void {
        Route::get('/admin/jamaah', [JamaahController::class, 'index'])->name('jamaahs.index');
        Route::get('/admin/jamaah/{jamaah}', [JamaahController::class, 'show'])->name('jamaahs.show');
    });
    Route::middleware('permission:jamaah.manage')->group(function (): void {
        Route::post('/admin/jamaah', [JamaahController::class, 'store'])->name('jamaahs.store');
        Route::put('/admin/jamaah/{jamaah}', [JamaahController::class, 'update'])->name('jamaahs.update');
        Route::delete('/admin/jamaah/{jamaah}', [JamaahController::class, 'destroy'])->name('jamaahs.destroy');
    });

    Route::middleware('permission:asset.view')->group(function (): void {
        Route::get('/aset', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/aset/{asset}', [AssetController::class, 'show'])->name('assets.show');
    });
    Route::middleware('permission:asset.manage')->group(function (): void {
        Route::post('/aset', [AssetController::class, 'store'])->name('assets.store');
        Route::patch('/aset/{asset}/status', [AssetController::class, 'updateStatus'])->name('assets.update-status');
    });
    Route::middleware('permission:asset.manage')->group(function (): void {
        Route::delete('/aset/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    });

    Route::middleware('permission:document.view')->group(function (): void {
        Route::get('/dokumen', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/dokumen/{document}', [DocumentController::class, 'show'])->name('documents.show');
        Route::get('/dokumen/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    });
    Route::middleware('permission:document.manage')->group(function (): void {
        Route::post('/dokumen', [DocumentController::class, 'store'])->name('documents.store');
        Route::patch('/dokumen/{document}/status', [DocumentController::class, 'updateStatus'])->name('documents.update-status');
    });
    Route::middleware('permission:document.delete')->group(function (): void {
        Route::delete('/dokumen/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    });

    Route::middleware('permission:announcement.view')->group(function (): void {
        Route::get('/admin/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');
    });
    Route::middleware('permission:announcement.manage')->group(function (): void {
        Route::post('/admin/pengumuman', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::patch('/admin/pengumuman/{announcement}/status', [AnnouncementController::class, 'updateStatus'])->name('announcements.update-status');
    });

    Route::middleware('permission:report.view')->group(function (): void {
        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    });
    Route::middleware('permission:report.export')->group(function (): void {
        Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');
    });
    Route::middleware('permission:audit_log.view')->group(function (): void {
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    Route::middleware('permission:setting.manage')->group(function (): void {
        Route::get('/pengaturan', [SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/pengaturan', [SettingsController::class, 'update'])->name('settings.update');
    });

    Route::middleware('permission:notification.manage')->group(function (): void {
        Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    });

    Route::middleware('permission:zakat.view')->group(function (): void {
        Route::get('/zakat', [ZakatController::class, 'index'])->name('zakat.index');
    });
    Route::middleware('permission:zakat.manage')->group(function (): void {
        Route::post('/zakat/muzakki', [ZakatController::class, 'storeMuzakki'])->name('zakat.muzakki.store');
        Route::post('/zakat/distribusi', [ZakatController::class, 'storeDistribusi'])->name('zakat.distribusi.store');
    });

    Route::middleware('permission:wakaf.view')->group(function (): void {
        Route::get('/wakaf', [WakafController::class, 'index'])->name('wakaf.index');
    });
    Route::middleware('permission:wakaf.manage')->group(function (): void {
        Route::post('/wakaf', [WakafController::class, 'store'])->name('wakaf.store');
    });

    Route::middleware('permission:qurban.view')->group(function (): void {
        Route::get('/qurban', [PlaceholderModuleController::class, 'qurban'])->name('qurban.index');
    });
});
