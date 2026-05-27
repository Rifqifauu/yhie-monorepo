<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // ── PUBLIC ────────────────────────────────────────────────────────────────

    /**
     * GET /api/settings
     * Semua setting publik dalam bentuk objek key-value datar.
     * Mudah dikonsumsi frontend: settings.site_name, settings.wa_number, dsb.
     */
    public function index(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');

        return response()->json($settings);
    }

    /**
     * GET /api/settings/{key}
     * Satu nilai setting berdasarkan key.
     */
    public function show(string $key): JsonResponse
    {
        $setting = Setting::where('key', $key)->firstOrFail();

        return response()->json($setting);
    }

    // ── ADMIN ─────────────────────────────────────────────────────────────────

    /**
     * POST /api/admin/settings
     * Buat setting baru. Key harus unik.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'key' => 'required|string|max:100|unique:settings,key',
            'value' => 'required|string',
        ]);

        $setting = Setting::create($data);

        return response()->json($setting, 201);
    }

    /**
     * PUT /api/admin/settings/{key}
     * Update nilai setting berdasarkan key (upsert).
     */
    public function update(Request $request, string $key): JsonResponse
    {
        $data = $request->validate([
            'value' => 'required|string',
        ]);

        $setting = Setting::updateOrCreate(['key' => $key], ['value' => $data['value']]);

        return response()->json($setting);
    }

    /**
     * DELETE /api/admin/settings/{key}
     */
    public function destroy(string $key): JsonResponse
    {
        Setting::where('key', $key)->firstOrFail()->delete();

        return response()->json(['message' => 'Setting deleted.'], 200);
    }
}
