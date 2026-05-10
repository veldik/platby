<?php

use App\Http\Controllers\Admin\CreditController;
use App\Http\Controllers\Admin\PayerController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentRecordController;
use App\Http\Controllers\Admin\PeriodPaymentController;
use App\Http\Controllers\Admin\PeriodPaymentPayerController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Sync\SyncPayerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPayerController;
use Illuminate\Support\Facades\Route;

// Sync API – chráněno statickým API klíčem (X-Api-Key header)
Route::group(['prefix' => 'sync', 'middleware' => 'api_key'], function () {
    Route::get('payers/lookup', [SyncPayerController::class, 'lookup']);
    Route::post('payers', [SyncPayerController::class, 'store']);
    Route::put('payers/{payer}', [SyncPayerController::class, 'update']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::post('/change-password', [AuthController::class, 'changePassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/', function () {
    return response()->json(['name' => config('app.name')]);
});

Route::group(['prefix' => '/', 'middleware' => 'auth:sanctum'], function () {
    Route::get('user', [UserController::class, 'index']);

    // User (payer) routes
    Route::group(['prefix' => 'payer', 'middleware' => 'is_payer'], function () {
        Route::get('/', [UserPayerController::class, 'show']);
        Route::get('add-credits', [UserPayerController::class, 'addCredits']);
        Route::get('payments', [UserPayerController::class, 'payments']);
        Route::get('credits', [UserPayerController::class, 'credits']);
        Route::prefix('payment-record/{paymentRecord}')->group(function () {
            Route::get('qrcode', [UserPayerController::class, 'qrCode']);
            Route::post('pay', [UserPayerController::class, 'payByCredits']);
        });
    });

    // Admin routes
    Route::group(['prefix' => 'admin', 'middleware' => 'is_admin'], function () {
        Route::get('stats', [StatsController::class, 'index']);
        Route::apiResource('payers', PayerController::class);
        Route::apiResource('payments', PaymentController::class);
        Route::prefix('payment-record/{paymentRecord}')->group(function () {
            Route::get('/', [PaymentRecordController::class, 'show']);
            Route::get('qrcode', [PaymentRecordController::class, 'qrCode']);
            Route::post('pay', [PaymentRecordController::class, 'pay']);
            Route::post('resend', [PaymentRecordController::class, 'resend']);
        });

        Route::prefix('payers/{payer}')->group(function () {
            Route::get('credits', [CreditController::class, 'index']);
            Route::post('credits', [CreditController::class, 'store']);
        });

        Route::apiResource('period-payments', PeriodPaymentController::class);

        Route::prefix('period-payment/{periodPayment}')->group(function () {
            Route::post('add-payer', [PeriodPaymentController::class, 'addPayer']);
        });

        Route::prefix('period-payment-payer/{periodPaymentPayer}')->group(function () {
            Route::delete('/', [PeriodPaymentPayerController::class, 'destroy']);
        });
    });
});
