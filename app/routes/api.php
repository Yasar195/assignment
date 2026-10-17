<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::post('/events', [AnalyticsController::class, 'ingest']);
Route::get('/analytics/dashboard', [AnalyticsController::class, 'dashboard']);

Route::get('/hello', function () {
    return response()->json([
        'message' => 'Hello, world!',
    ]);
});
