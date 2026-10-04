<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\PrayerTimesController;
use App\Http\Controllers\QurbanSavingsController;
use App\Http\Controllers\SlaughteringController;
use App\Http\Controllers\VolunteerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Public\PortalController;

Route::get('/', [PortalController::class, 'home'])->name('public.home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/qurban/dashboard', DashboardController::class)->name('qurban.dashboard');

    Route::prefix('qurban')->middleware('role:admin,super-admin,panitia')->group(function (): void {
        Route::get('savings', [QurbanSavingsController::class, 'index'])->name('savings.index');
        Route::get('savings/{saving}', [QurbanSavingsController::class, 'show'])->name('savings.show');
        Route::post('savings', [QurbanSavingsController::class, 'store'])->name('savings.store');
        Route::put('savings/{saving}', [QurbanSavingsController::class, 'update'])->name('savings.update');
        Route::delete('savings/{saving}', [QurbanSavingsController::class, 'destroy'])->name('savings.destroy');
        Route::post('savings/{saving}/topup', [QurbanSavingsController::class, 'topup'])->name('savings.topup');
        Route::post('savings/{saving}/withdraw', [QurbanSavingsController::class, 'withdraw'])->name('savings.withdraw');

        Route::get('animals', [AnimalController::class, 'index'])->name('animals.index');
        Route::get('animals/{animal}', [AnimalController::class, 'show'])->name('animals.show');
        Route::post('animals', [AnimalController::class, 'store'])->name('animals.store');
        Route::put('animals/{animal}', [AnimalController::class, 'update'])->name('animals.update');
        Route::delete('animals/{animal}', [AnimalController::class, 'destroy'])->name('animals.destroy');

        Route::get('participants', [ParticipantController::class, 'index'])->name('participants.index');
        Route::get('participants/{participant}', [ParticipantController::class, 'show'])->name('participants.show');
        Route::post('participants', [ParticipantController::class, 'store'])->name('participants.store');
        Route::put('participants/{participant}', [ParticipantController::class, 'update'])->name('participants.update');
        Route::delete('participants/{participant}', [ParticipantController::class, 'destroy'])->name('participants.destroy');
        Route::post('participants/auto-group', [ParticipantController::class, 'autoGroup'])->name('participants.auto-group');

        Route::get('slaughterings', [SlaughteringController::class, 'index'])->name('slaughterings.index');
        Route::get('slaughterings/{slaughtering}', [SlaughteringController::class, 'show'])->name('slaughterings.show');
        Route::post('slaughterings', [SlaughteringController::class, 'store'])->name('slaughterings.store');
        Route::put('slaughterings/{slaughtering}', [SlaughteringController::class, 'update'])->name('slaughterings.update');
        Route::delete('slaughterings/{slaughtering}', [SlaughteringController::class, 'destroy'])->name('slaughterings.destroy');
        Route::post('slaughterings/{slaughtering}/assign-volunteers', [SlaughteringController::class, 'assignVolunteers'])->name('slaughterings.assign-volunteers');
        Route::post('slaughterings/{slaughtering}/mark-cut', [SlaughteringController::class, 'markCut'])->name('slaughterings.mark-cut');

        Route::get('volunteers', [VolunteerController::class, 'index'])->name('volunteers.index');
        Route::get('volunteers/{volunteer}', [VolunteerController::class, 'show'])->name('volunteers.show');
        Route::post('volunteers', [VolunteerController::class, 'store'])->name('volunteers.store');
        Route::put('volunteers/{volunteer}', [VolunteerController::class, 'update'])->name('volunteers.update');
        Route::delete('volunteers/{volunteer}', [VolunteerController::class, 'destroy'])->name('volunteers.destroy');
        Route::get('volunteers/{volunteer}/id-card-pdf', [VolunteerController::class, 'idCardPdf'])->name('volunteers.id-card-pdf');
        Route::get('volunteers-id-card-batch-pdf', [VolunteerController::class, 'batchIdCardPdf'])->name('volunteers.id-card-batch-pdf');

        Route::get('distributions', [DistributionController::class, 'index'])->name('distributions.index');
        Route::get('distributions/{distribution}', [DistributionController::class, 'show'])->name('distributions.show');
        Route::post('distributions', [DistributionController::class, 'store'])->name('distributions.store');
        Route::put('distributions/{distribution}', [DistributionController::class, 'update'])->name('distributions.update');
        Route::delete('distributions/{distribution}', [DistributionController::class, 'destroy'])->name('distributions.destroy');
        Route::post('distributions/{distribution}/mark-delivered', [DistributionController::class, 'markDelivered'])->name('distributions.mark-delivered');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public routes
Route::get('/jadwal-shalat', [PrayerTimesController::class, 'index'])
    ->name('public.prayer-times');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/public.php';
