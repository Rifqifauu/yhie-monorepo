<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class SettingController extends Controller
{
    // Key yang tidak boleh pernah bocor lewat endpoint publik (index/show),
    // meski tetap dikelola lewat mekanisme Setting yang sama oleh admin.
    protected array $publicHidden = ['login_passcode'];

    // Key setting yang nilainya berupa gambar ter-upload - dibatasi biar
    // endpoint upload nggak bisa dipakai buat nulis sembarang key.
    protected array $uploadableImageKeys = ['logo_url', 'favicon_url'];

    // ── PUBLIC ────────────────────────────────────────────────────────────────

    /**
     * GET /api/settings
     * Semua setting publik dalam bentuk objek key-value datar.
     * Mudah dikonsumsi frontend: settings.site_name, settings.wa_number, dsb.
     */
    public function index(): JsonResponse
    {
        try {
            $settings = Setting::whereNotIn('key', $this->publicHidden)->get();

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
        if (in_array($key, $this->publicHidden)) {
            return response()->json([
                'message' => 'Setting not found.',
                'data' => null
            ], 404);
        }

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
     * POST /api/admin/settings/{key}/upload
     * Upload gambar (logo/favicon) dan simpan path-nya sebagai value setting.
     */
    public function uploadImage(Request $request, string $key): JsonResponse
    {
        if (!in_array($key, $this->uploadableImageKeys)) {
            return response()->json([
                'message' => 'Key ini tidak mendukung upload gambar.'
            ], 422);
        }

        $data = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,webp,ico|max:2048',
        ]);

        try {
            $existing = Setting::where('key', $key)->value('value');
            if ($existing && str_starts_with($existing, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $existing));
            }

            $path = '/storage/' . $data['image']->store('settings', 'public');

            $setting = Setting::updateOrCreate(['key' => $key], ['value' => $path]);

            return response()->json([
                'message' => 'Gambar berhasil diunggah.',
                'data' => $setting,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error uploading setting image: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengunggah gambar.'
            ], 500);
        }
    }

    /**
     * POST /api/admin/settings/hero-images
     * Tambah satu gambar ke slider hero section (disimpan sbg JSON array
     * di dalam value setting "hero_images").
     */
    public function addHeroImage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            $images = json_decode(Setting::get('hero_images', '[]'), true) ?: [];
            $images[] = '/storage/' . $data['image']->store('hero', 'public');

            $setting = Setting::updateOrCreate(
                ['key' => 'hero_images'],
                ['value' => json_encode($images)],
            );

            return response()->json([
                'message' => 'Gambar hero berhasil ditambahkan.',
                'data' => $setting,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error adding hero image: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan gambar.'
            ], 500);
        }
    }

    /**
     * DELETE /api/admin/settings/hero-images
     * Hapus satu gambar dari slider hero section berdasarkan path-nya.
     */
    public function removeHeroImage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path' => 'required|string',
        ]);

        try {
            $images = json_decode(Setting::get('hero_images', '[]'), true) ?: [];
            $images = array_values(array_filter($images, fn ($img) => $img !== $data['path']));

            if (str_starts_with($data['path'], '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $data['path']));
            }

            $setting = Setting::updateOrCreate(
                ['key' => 'hero_images'],
                ['value' => json_encode($images)],
            );

            return response()->json([
                'message' => 'Gambar hero berhasil dihapus.',
                'data' => $setting,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error removing hero image: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus gambar.'
            ], 500);
        }
    }

    /**
     * POST /api/admin/settings/bulk
     * Update multiple settings at once.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);

        try {
            foreach ($data['settings'] as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value ?? '']
                );
            }

            return response()->json([
                'message' => 'Settings updated successfully.'
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error bulk updating settings: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to bulk update settings.'
            ], 500);
        }
    }

    /**
     * DELETE /api/admin/settings/{key}
     */
    public function destroy(string $key): JsonResponse
    {
        try {
            $setting = Setting::where('key', $key)->firstOrFail();
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
