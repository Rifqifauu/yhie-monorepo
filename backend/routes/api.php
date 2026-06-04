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

// Settings
Route::prefix("settings")->group(function () {
    Route::get("/", [SettingController::class, "index"]);
    Route::get("/{key}", [SettingController::class, "show"]);
});

// Program Registration — public endpoint (tanpa auth)
Route::post("program-registrations", [ProgramRegistrationController::class, "store"]);

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
Route::apiResource("media", MediaController::class)->except(["show", "index"]);
Route::apiResource("partners", PartnerController::class)->except([
    "show",
    "index",
]);
Route::apiResource("settings", SettingController::class)->except([
    "show",
    "index",
]);
Route::apiResource("program-registrations", ProgramRegistrationController::class)->except([
    "store",
]);
