<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirQualityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Models\AdditionalAirQualityReading;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::view('/', 'dashboard')->name('home');
Route::view('/analytics', 'analytics')->name('charts');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');
});

/*
|--------------------------------------------------------------------------
| Air Quality Data Routes
|--------------------------------------------------------------------------
*/
Route::controller(AirQualityController::class)->group(function () {
    Route::get('/store-reading', 'captureAtmosphericMetrics');
    Route::get('/get-readings', 'retrieveAtmosphericData');
    Route::get('/trigger-store-reading', 'initiateDataCapture')->name('trigger.store.reading');
    Route::get('/api/air-quality-readings', 'retrieveAtmosphericData');
});

/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
*/
Route::prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'getNotifications']);

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('/', [NotificationController::class, 'store']);
        Route::get('/users', [NotificationController::class, 'getUsers']);
    });
});

/*
|--------------------------------------------------------------------------
| Additional Air Quality Readings API
|--------------------------------------------------------------------------
*/
Route::get('/api/additional-air-quality-readings', function () {
    return AdditionalAirQualityReading::with('airQualityReading')
        ->orderBy('reading_time', 'desc')
        ->take(30)
        ->get();
})->middleware('api'); // Consider adding API authentication
