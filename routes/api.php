<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QrController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/participants/{participant}/payments', [PaymentController::class, 'recordParticipantPayment']);
    Route::post('/notifications/participants/{participant}/payment-reminder', [NotificationController::class, 'paymentReminder']);
    Route::post('/notifications/volunteers/{volunteer}/assignment', [NotificationController::class, 'volunteerAssignment']);
});

Route::post('/payments/mock-gateway-callback', [PaymentController::class, 'mockGatewayCallback']);
Route::get('/qr/animals/{token}', [QrController::class, 'showAnimalByQr']);
Route::get('/qr/volunteers/{token}', [QrController::class, 'showVolunteerByQr']);
