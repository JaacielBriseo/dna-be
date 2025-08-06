<?php


use App\Http\Controllers\DnaRecordController;

use Illuminate\Support\Facades\Route;

Route::name('api')->group(function () {
    Route::post('/mutation', [DnaRecordController::class, 'analyze'])->middleware(['throttle:mutation']);
    Route::get('/stats', [DnaRecordController::class, 'stats']);
    Route::get('/list', [DnaRecordController::class, 'list']);
});
