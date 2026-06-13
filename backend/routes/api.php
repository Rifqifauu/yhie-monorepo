<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgramRegistrationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Tidak butuh cookie)
|--------------------------------------------------------------------------
| Digunakan untuk menampilkan data ke pengunjung website (Frontend)
| dan menerima *submit* form pendaftaran awal.
*/

Route::prefix("articles")->group(function () {
    Route::get("/", [ArticleController::class, "index"]);
    Route::get("/{slug}", [ArticleController::class, "show"]);
});

Route::prefix("programs")->group(function () {
    Route::get("/", [ProgramController::class, "index"]);
    Route::get("/{slug}", [ProgramController::class, "show"]);
});

Route::prefix("schedules")->group(function () {
    Route::get("/", [ScheduleController::class, "index"]);
    Route::get("/{id}", [ScheduleController::class, "show"]);
});

Route::prefix("media")->group(function () {
    Route::get("/", [MediaController::class, "index"]);
    Route::get("/{slug}", [MediaController::class, "show"]);
});

Route::prefix("partners")->group(function () {
    Route::get("/", [PartnerController::class, "index"]);
    Route::get("/{slug}", [PartnerController::class, "show"]);
});

Route::prefix("settings")->group(function () {
    Route::get("/", [SettingController::class, "index"]);
    Route::get("/{key}", [SettingController::class, "show"]);
});

// Pendaftaran program biasanya bersifat publik
Route::post("program-registrations", [
    ProgramRegistrationController::class,
    "store",
]);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (Butuh cookie Sanctum)
|--------------------------------------------------------------------------
| Digunakan untuk manajemen data (CRUD) oleh Admin atau operasi
| yang mewajibkan user login.
*/

Route::middleware("auth:sanctum")->group(function () {
    Route::get("/user", function (Request $request) {
        return $request->user();
    });

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

    // Transaksi biasanya membutuhkan user untuk login
    Route::apiResource("transactions", TransactionController::class);

    // Manajemen pendaftaran program (index, show, update, destroy) oleh Admin
    Route::apiResource(
        "program-registrations",
        ProgramRegistrationController::class,
    )->except(["store"]);
});
