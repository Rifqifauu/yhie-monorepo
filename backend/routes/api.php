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
Route::prefix("articles")->group(function () {
    Route::get("/", [ArticleController::class, "index"]);
    Route::get("/{slug}", [ArticleController::class, "show"]);
});

// Programs
Route::prefix("programs")->group(function () {
    Route::get("/", [ProgramController::class, "index"]);
    Route::get("/{slug}", [ProgramController::class, "show"]);
});

// Schedules
Route::prefix("schedules")->group(function () {
    Route::get("/", [ScheduleController::class, "index"]);
    Route::get("/{id}", [ScheduleController::class, "show"]);
});

// Media / Galeri
Route::prefix("media")->group(function () {
    Route::get("/", [MediaController::class, "index"]);
    Route::get("/{slug}", [MediaController::class, "show"]);
});

// Partners
Route::prefix("partners")->group(function () {
    Route::get("/", [PartnerController::class, "index"]);
    Route::get("/{slug}", [PartnerController::class, "show"]);
});

// Settings
Route::prefix("settings")->group(function () {
    Route::get("/", [SettingController::class, "index"]);
    Route::get("/{key}", [SettingController::class, "show"]);
});
// ─────────────────────────────────────────────────────────────────────────────
// Admin Routes — auth:sanctum,  api bisa langsung pake apiResource (include semua CRUD)
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware("auth:sanctum")->group(function () {
    Route::apiResource("articles", ArticleController::class)->except([
        "show",
        "index",
    ]);
    Route::apiResource("programs", ProgramController::class)->except([
        "show",
        "index",
    ]);
    Route::apiResource("schedules", ScheduleController::class)->except([
        "show",
        "index",
    ]);
    Route::apiResource("media", MediaController::class)->except([
        "show",
        "index",
    ]);
    Route::apiResource("partners", PartnerController::class)->except([
        "show",
        "index",
    ]);
    Route::apiResource("settings", SettingController::class)->except([
        "show",
        "index",
    ]);
});
