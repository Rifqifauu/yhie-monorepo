<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk manajemen file

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        try {
            $media = Media::orderBy('created_at', 'desc')
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('title_id', 'like', "%{$search}%")
                          ->orWhere('title_en', 'like', "%{$search}%");
                    });
                })->paginate(10);

            return response()->json([
                'message' => 'Media fetched successfully.',
                'data'    => $media
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error fetching media: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch media.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_id'       => 'required|string|max:255',
            'title_en'       => 'required|string|max:255',
            'description_id' => 'required|string',
            'description_en' => 'required|string',
            'slug_id'        => 'required|string|unique:media,slug_id',
            'slug_en'        => 'required|string|unique:media,slug_en',
            
            // Validasi untuk file upload
            'image'          => 'required|array',
            'image.*'        => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Maksimal 2MB per gambar
        ]);

        try {
            $imagePaths = [];

            // Proses upload file jika ada
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $file) {
                    // Simpan file ke folder storage/app/public/media
                    $path = $file->store('media', 'public');
                    
                    // Simpan path-nya dengan awalan /storage/ agar mudah diakses frontend
                    $imagePaths[] = '/storage/' . $path;
                }
            }

            // Timpa array input 'image' dengan array path yang baru saja di-generate
            $data['image'] = $imagePaths;

            $media = Media::create($data);

            return response()->json([
                'message' => 'Media created successfully.',
                'data'    => $media
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Error creating media: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create media.'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $media = Media::findOrFail($id);

            return response()->json([
                'message' => 'Media fetched successfully.',
                'data'    => $media
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Media not found.'
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error fetching media detail: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch media detail. Please try again later.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title_id'       => 'sometimes|required|string|max:255',
            'title_en'       => 'sometimes|required|string|max:255',
            'description_id' => 'sometimes|required|string',
            'description_en' => 'sometimes|required|string',
            'slug_id'        => ['sometimes', 'string', Rule::unique('media')->ignore($id)],
            'slug_en'        => ['sometimes', 'string', Rule::unique('media')->ignore($id)],
            
            // Validasi file opsional saat update
            'image'          => 'sometimes|required|array',
            'image.*'        => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            $media = Media::findOrFail($id);

            // Cek apakah ada upload gambar baru
            if ($request->hasFile('image')) {
                // 1. Hapus gambar lama dari storage server terlebih dahulu
                if (is_array($media->image)) {
                    foreach ($media->image as $oldImage) {
                        // Hilangkan '/storage/' untuk mendapatkan path asli 'media/namafile.jpg'
                        $oldPath = str_replace('/storage/', '', $oldImage);
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                // 2. Upload gambar baru
                $newImagePaths = [];
                foreach ($request->file('image') as $file) {
                    $path = $file->store('media', 'public');
                    $newImagePaths[] = '/storage/' . $path;
                }

                // Timpa data validasi dengan array path gambar yang baru
                $validated['image'] = $newImagePaths;
            }

            $media->update($validated);

            return response()->json([
                'message' => 'Media updated successfully.',
                'data'    => $media
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Media not found.',
                'data'    => null
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error updating media: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update media. Please try again later.',
                'data'    => null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $media = Media::findOrFail($id);

            // Hapus semua file gambar yang terkait dengan media ini dari storage
            if (is_array($media->image)) {
                foreach ($media->image as $oldImage) {
                    $oldPath = str_replace('/storage/', '', $oldImage);
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $media->delete();

            return response()->json([
                'message' => 'Media deleted successfully.',
                'data'    => null
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Media not found.',
                'data'    => null
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error deleting media: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete media. Please try again later.',
                'data'    => null
            ], 500);
        }
    }
}