<?php

use App\Http\Controllers\Api\AccountAuthController;
use App\Http\Controllers\Api\AdminDestinationController;
use App\Http\Controllers\Api\AuditEventController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\DestinationPhotoController;
use App\Http\Controllers\Api\DestinationUpdateController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ItineraryController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\TaxonomyController;
use Illuminate\Support\Facades\Route;

Route::get('/destinations', [DestinationController::class, 'index']);
Route::get('/destinations/{destination}', [DestinationController::class, 'show']);
Route::get('/categories', [TaxonomyController::class, 'categories']);
Route::get('/municipalities', [TaxonomyController::class, 'municipalities']);

Route::post('/account/register', [AccountAuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/account/login', [AccountAuthController::class, 'login'])->middleware('throttle:5,1');
Route::middleware('auth:sanctum')->prefix('account')->group(function () {
    Route::get('/me', [AccountAuthController::class, 'me']);
    Route::post('/logout', [AccountAuthController::class, 'logout']);
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/sync', [FavoriteController::class, 'sync']);
    Route::put('/favorites/{destination}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{destination}', [FavoriteController::class, 'destroy']);
    Route::get('/itineraries', [ItineraryController::class, 'index']);
    Route::post('/itineraries', [ItineraryController::class, 'store']);
    Route::put('/itineraries/{itinerary}', [ItineraryController::class, 'update']);
    Route::delete('/itineraries/{itinerary}', [ItineraryController::class, 'destroy']);
    Route::post('/itineraries/{itinerary}/destinations/{destination}', [ItineraryController::class, 'addStop']);
    Route::put('/itineraries/{itinerary}/stops/reorder', [ItineraryController::class, 'reorder']);
    Route::put('/itineraries/{itinerary}/stops/{stop}', [ItineraryController::class, 'updateStop']);
    Route::delete('/itineraries/{itinerary}/stops/{stop}', [ItineraryController::class, 'removeStop']);
});

Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::middleware(['auth:sanctum', 'staff'])->prefix('admin')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('destinations', AdminDestinationController::class)->except('show');
    Route::post('/destinations/{destination}/updates', [DestinationUpdateController::class, 'store']);
    Route::put('/updates/{update}', [DestinationUpdateController::class, 'update']);
    Route::delete('/updates/{update}', [DestinationUpdateController::class, 'destroy']);
    Route::post('/destinations/{destination}/photos', [DestinationPhotoController::class, 'store']);
    Route::put('/destinations/{destination}/photos/reorder', [DestinationPhotoController::class, 'reorder']);
    Route::delete('/destinations/{destination}/photos/{photo}', [DestinationPhotoController::class, 'destroy']);
    Route::get('/audit-events', [AuditEventController::class, 'index']);
    Route::middleware('admin')->group(function () {
        Route::get('/staff', [StaffController::class, 'index']);
        Route::post('/staff', [StaffController::class, 'store']);
        Route::put('/staff/{staff}', [StaffController::class, 'update']);
    });
});
