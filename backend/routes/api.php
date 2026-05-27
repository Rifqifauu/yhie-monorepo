<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| API Routes — YHIE (Yayasan Hafiz Indonesia Emas)
|--------------------------------------------------------------------------
|
| Public routes  : dapat diakses tanpa token (untuk Nuxt SSG)
| Admin routes   : dilindungi middleware auth:sanctum
|
*/

// ─────────────────────────────────────────────────────────────────────────────
// PUBLIC ROUTES
// ─────────────────────────────────────────────────────────────────────────────

// Articles
Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/published', [ArticleController::class, 'published']);
    Route::get('/category/{category}', [ArticleController::class, 'byCategory']);
    Route::get('/{slug}', [ArticleController::class, 'show']);
});

// Programs
Route::prefix('programs')->group(function () {
    Route::get('/', [ProgramController::class, 'index']);
    Route::get('/{slug}', [ProgramController::class, 'show']);
});

// Schedules
Route::prefix('schedules')->group(function () {
    Route::get('/', [ScheduleController::class, 'index']);
    Route::get('/upcoming', [ScheduleController::class, 'upcoming']);
    Route::get('/{id}', [ScheduleController::class, 'show']);
});

// Media / Galeri
Route::prefix('media')->group(function () {
    Route::get('/', [MediaController::class, 'index']);
    Route::get('/{slug}', [MediaController::class, 'show']);
});

// Partners
Route::prefix('partners')->group(function () {
    Route::get('/', [PartnerController::class, 'index']);
    Route::get('/{slug}', [PartnerController::class, 'show']);
});

// Settings
Route::prefix('settings')->group(function () {
    Route::get('/', [SettingController::class, 'index']);
    Route::get('/{key}', [SettingController::class, 'show']);
});

// ─────────────────────────────────────────────────────────────────────────────
// ADMIN ROUTES — auth:sanctum
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', fn(Request $request) => $request->user());

    // Articles
    Route::prefix('admin/articles')->group(function () {
        Route::get('/', [ArticleController::class, 'adminIndex']);
        Route::post('/', [ArticleController::class, 'store']);
        Route::put('/{id}', [ArticleController::class, 'update']);
        Route::patch('/{id}/publish', [ArticleController::class, 'togglePublish']);
        Route::delete('/{id}', [ArticleController::class, 'destroy']);
    });

    // Programs
    Route::prefix('admin/programs')->group(function () {
        Route::post('/', [ProgramController::class, 'store']);
        Route::put('/{id}', [ProgramController::class, 'update']);
        Route::delete('/{id}', [ProgramController::class, 'destroy']);
    });

    // Schedules
    Route::prefix('admin/schedules')->group(function () {
        Route::post('/', [ScheduleController::class, 'store']);
        Route::put('/{id}', [ScheduleController::class, 'update']);
        Route::delete('/{id}', [ScheduleController::class, 'destroy']);
    });

    // Media
    Route::prefix('admin/media')->group(function () {
        Route::post('/', [MediaController::class, 'store']);
        Route::put('/{id}', [MediaController::class, 'update']);
        Route::delete('/{id}', [MediaController::class, 'destroy']);
    });

    // Partners
    Route::prefix('admin/partners')->group(function () {
        Route::post('/', [PartnerController::class, 'store']);
        Route::put('/{id}', [PartnerController::class, 'update']);
        Route::delete('/{id}', [PartnerController::class, 'destroy']);
    });

    // Settings
    Route::prefix('admin/settings')->group(function () {
        Route::post('/', [SettingController::class, 'store']);
        Route::put('/{key}', [SettingController::class, 'update']);
        Route::delete('/{key}', [SettingController::class, 'destroy']);
    });
});