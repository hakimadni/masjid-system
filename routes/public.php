<?php

use App\Http\Controllers\Public\PortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Portal Routes
|--------------------------------------------------------------------------
|
| Routes accessible without authentication for jamaah and public visitors.
|
*/

Route::get('/', [PortalController::class, 'home'])->name('public.home');
Route::get('/profil', [PortalController::class, 'profile'])->name('public.profile');
Route::get('/jadwal', [PortalController::class, 'schedule'])->name('public.schedule');
Route::get('/kajian', [PortalController::class, 'events'])->name('public.events');
Route::get('/pengumuman', [PortalController::class, 'announcements'])->name('public.announcements');
Route::get('/donasi', [PortalController::class, 'donation'])->name('public.donation');
Route::get('/laporan-keuangan', [PortalController::class, 'financeReport'])->name('public.finance-report');
Route::get('/kontak', [PortalController::class, 'contact'])->name('public.contact');