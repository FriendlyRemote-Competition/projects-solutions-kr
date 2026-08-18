<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* auth */
Route::post('/admin/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

/* lines */
Route::get('/lines', [\App\Http\Controllers\Api\LineController::class, 'index']);
Route::get('/lines/{line:code}', [\App\Http\Controllers\Api\LineController::class, 'show']);
Route::get('/lines/{line:code}/timetable', [\App\Http\Controllers\Api\LineController::class, 'timetable']);

/* bookings */
Route::post('/bookings', [\App\Http\Controllers\BookingController::class, 'store']);
Route::post('/bookings/lookup', [\App\Http\Controllers\BookingController::class, 'lookup']);
Route::patch('/bookings/{code}', [\App\Http\Controllers\BookingController::class, 'update']);
Route::post('/bookings/{code}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel']);

/* user login */
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/bookings', [\App\Http\Controllers\Api\AdminController::class, 'bookings']);
    Route::post('/departures/{code}/cancel', [\App\Http\Controllers\Api\AdminController::class, 'cancelBooking']);

    // admin only
    Route::middleware('can:admin')->group(function () {
        Route::post('/lines', [\App\Http\Controllers\Api\AdminController::class, 'lineStore']);
        Route::put('/lines/{line:code}', [\App\Http\Controllers\Api\AdminController::class, 'lineUpdate']);
        Route::post('/lines/{line:code}/service-windows', [\App\Http\Controllers\Api\AdminController::class, 'serviceWindowStore']);
        Route::delete('/lines/{line:code}/service-windows/{start_time}', [\App\Http\Controllers\Api\AdminController::class, 'serviceWindowDestroy']);
    });
});

