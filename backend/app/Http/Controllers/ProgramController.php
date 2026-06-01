<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException; // Tambahkan ini untuk handle 404

class ProgramController extends Controller
{
    // ── PUBLIC ────────────────────────────────────────────────────────────────

    /**
     * GET /api/programs
     * Semua program sertifikasi (Hafiz & Maulana).
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');

        try {
            $programs = Program::orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('title_id', 'like', "%{$search}%")
                            ->orWhere('title_en', 'like', "%{$search}%");
                    });
                })->paginate(10);

            return response()->json([
                'message' => 'Programs fetched successfully.',
                'data' => $programs
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error fetching programs: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch programs.'
            ], 500);
        }
    }

    /**
     * GET /api/programs/{slug}
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $lang = $request->query('lang', 'id');
        $column = $lang === 'en' ? 'slug_en' : 'slug_id';

        try {
            $program = Program::where($column, $slug)->firstOrFail();

            return response()->json([
                'message' => 'Program fetched successfully.',
                'data' => $program
            ], 200);

        } catch (ModelNotFoundException $e) {
            // Tangkap jika firstOrFail() gagal menemukan data
            return response()->json([
                'message' => 'Program not found.'
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error fetching program detail: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch program detail. Please try again later.'
            ], 500);
        }
    }

    // ── ADMIN ─────────────────────────────────────────────────────────────────

    /**
     * POST /api/programs
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'title_id' => 'required|string|max:255',
            'description_id' => 'required|string',
            'title_en' => 'required|string|max:255',
            'description_en' => 'required|string',
            'price_id' => 'required|numeric|min:0',
            'price_en' => 'required|numeric|min:0',
            'slug_id' => 'required|string|unique:programs,slug_id',
            'slug_en' => 'required|string|unique:programs,slug_en',
        ];

        if ($request->hasFile('image_path')) {
            $rules['image_path'] = 'required|image|mimes:jpeg,png,jpg,webp|max:2048';
        } else {
            $rules['image_path'] = 'required|string|max:255';
        }

        $data = $request->validate($rules);

        try {
            if ($request->hasFile('image_path')) {
                $data['image_path'] = $this->storeProgramImage($request->file('image_path'));
            }

            $program = Program::create($data);

            return response()->json([
                'message' => 'Program created successfully.',
                'data' => $program
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Error creating program: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create program. Please try again later.'
            ], 500);
        }
    }

    /**
     * PUT /api/programs/{program}
     */
    public function update(Request $request, Program $program): JsonResponse
    {
        $rules = [
            'title_id' => 'sometimes|string|max:255',
            'description_id' => 'sometimes|string',
            'title_en' => 'sometimes|string|max:255',
            'description_en' => 'sometimes|string',
            'price_id' => 'sometimes|numeric|min:0',
            'price_en' => 'sometimes|numeric|min:0',
            'slug_id' => ['sometimes', 'string', Rule::unique('programs')->ignore($program->id)],
            'slug_en' => ['sometimes', 'string', Rule::unique('programs')->ignore($program->id)],
        ];

        if ($request->hasFile('image_path')) {
            $rules['image_path'] = 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048';
        } elseif ($request->has('image_path')) {
            $rules['image_path'] = 'sometimes|string|max:255';
        }

        $data = $request->validate($rules);

        try {
            if ($request->hasFile('image_path')) {
                $data['image_path'] = $this->storeProgramImage($request->file('image_path'), $program);
            }

            $program->update($data);

            return response()->json([
                'message' => 'Program updated successfully.',
                'data' => $program
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error updating program: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update program. Please try again later.'
            ], 500);
        }
    }

    /**
     * DELETE /api/programs/{program}
     */
    public function destroy(Program $program): JsonResponse
    {
        try {
            $this->deleteStoredImage($program->image_path);

            $program->delete();

            return response()->json([
                'message' => 'Program deleted successfully.'
            ], 200); // 200 OK karena kita mengirimkan pesan (message) di response body

        } catch (\Throwable $e) {
            Log::error('Error deleting program: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete program. Please try again later.'
            ], 500);
        }
    }

    private function storeProgramImage($imageFile, ?Program $program = null): string
    {
        if ($program !== null) {
            $this->deleteStoredImage($program->image_path);
        }

        $path = $imageFile->store('programs', 'public');

        return '/storage/' . $path;
    }

    private function deleteStoredImage(?string $imagePath): void
    {
        if (empty($imagePath) || !str_starts_with($imagePath, '/storage/')) {
            return;
        }

        $path = str_replace('/storage/', '', $imagePath);
        Storage::disk('public')->delete($path);
    }
}