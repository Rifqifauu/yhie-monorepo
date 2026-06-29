<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

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
        try {
            $settings = Setting::all();

            return response()->json([
                'message' => 'Settings fetched successfully.',
                'data' => $settings
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error fetching settings: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch settings.'
            ], 500);
        }
    }

    /**
     * GET /api/settings/{key}
     * Satu nilai setting berdasarkan key.
     */
    public function show(string $key): JsonResponse
    {
        try {
            $setting = Setting::where('key', $key)->firstOrFail();

            return response()->json([
                'message' => 'Setting fetched successfully.',
                'data' => $setting
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Setting not found.',
                'data' => null
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error fetching setting detail: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch setting detail.'
            ], 500);
        }
    }

    // ── ADMIN ─────────────────────────────────────────────────────────────────

    /**
     * POST /api/admin/settings
     * Buat setting baru. Key harus unik.
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Gunakan Validator::make agar kita bisa mengontrol response jika gagal
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:100|unique:settings,key',
            'value' => 'required|string',
        ]);

        // 2. Tangkap error validasi (termasuk jika key sudah ada / duplikat)
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // Eksekusi query menggunakan data yang sudah tervalidasi
            $setting = Setting::create($validator->validated());

            return response()->json([
                'message' => 'Setting created successfully.',
                'data' => $setting
            ], 201);

        } catch (QueryException $e) {
            // 3. (Opsional) Tangkap error di level Database jika validasi entah bagaimana tembus
            // 1062 adalah kode error MySQL untuk "Duplicate entry"
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'message' => 'Key setting sudah digunakan. Silakan gunakan key lain.'
                ], 409); // 409 Conflict
            }

            Log::error('Database error creating setting: ' . $e->getMessage());
            return response()->json([
                'message' => 'Database error occurred while creating setting.'
            ], 500);

        } catch (\Throwable $e) {
            Log::error('Error creating setting: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create setting.'
            ], 500);
        }
    }
    public function update(Request $request, string $key): JsonResponse
    {
        $data = $request->validate([
            'value' => 'nullable|string',
        ]);

        try {
            $setting = Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $data['value'] ?? '']
            );

            return response()->json([
                'message' => 'Setting updated successfully.',
                'data' => $setting
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error updating setting: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update setting.'
            ], 500);
        }
    }

    /**
     * DELETE /api/admin/settings/{key}
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $setting = Setting::where('id', $id)->firstOrFail();
            $setting->delete();

            return response()->json([
                'message' => 'Setting deleted successfully.',
                'data' => null
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Setting not found.',
                'data' => null
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error deleting setting: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete setting.',
                'data' => null
            ], 500);
        }
    }
}
