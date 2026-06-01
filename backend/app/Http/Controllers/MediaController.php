<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MediaController extends Controller
{
    // ── PUBLIC ────────────────────────────────────────────────────────────────

    /**
     * GET /api/media
     * Semua galeri foto/video.
     */
    public function index(Request $request): JsonResponse
    {
        $media = Media::latest()
            ->paginate($request->integer('per_page', 12));

        return response()->json($media);
    }

    /**
     * GET /api/media/{slug}
     * Detail satu item galeri.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $lang = $request->query('lang', 'id');
        $column = $lang === 'en' ? 'slug_en' : 'slug_id';

        $media = Media::where($column, $slug)->firstOrFail();

        return response()->json($media);
    }

    // ── ADMIN ─────────────────────────────────────────────────────────────────

    /**
     * POST /api/admin/media
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title_id' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_id' => 'required|string',
            'description_en' => 'required|string',
            'image' => 'required|array',
            'slug_id' => 'required|string|unique:media,slug_id',
            'slug_en' => 'required|string|unique:media,slug_en',
        ]);

        $media = Media::create($data);

        return response()->json($media, 201);
    }

    /**
     * PUT /api/admin/media/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $media = Media::findOrFail($id);

        $data = $request->validate([
            'title_id' => 'sometimes|string|max:255',
            'title_en' => 'sometimes|string|max:255',
            'description_id' => 'sometimes|string',
            'description_en' => 'sometimes|string',
            'image' => 'sometimes|array',
            'slug_id' => ['sometimes', 'string', Rule::unique('media')->ignore($id)],
            'slug_en' => ['sometimes', 'string', Rule::unique('media')->ignore($id)],
        ]);

        $media->update($data);

        return response()->json($media);
    }

    /**
     * DELETE /api/admin/media/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        Media::findOrFail($id)->delete();

        return response()->json(['message' => 'Media deleted.'], 200);
    }
}
