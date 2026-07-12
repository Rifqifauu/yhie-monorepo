<?php

namespace App\Http\Controllers;

use App\Enums\ContentCategory;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Tambahan untuk manipulasi string
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query("search");
        $category = $request->query("category");

        // Menggunakan pluck() agar hasil berupa array of strings yang rapi: ["Berita", "Artikel"]
        // Ditambah whereNotNull agar tidak mengambil kategori yang kosong (jika ada)
        $existingCategory = Media::select("category")
            ->whereNotNull("category")
            ->distinct()
            ->pluck("category");

        try {
            $media = Media::orderBy("created_at", "desc")
                ->orderBy("id", "desc")
                ->when($category, function ($query, $category) {
                    // Case-insensitive supaya filter tetap cocok walau beda kapitalisasi
                    return $query->whereRaw("LOWER(category) = ?", [
                        Str::lower($category),
                    ]);
                })
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where("title_id", "like", "%{$search}%")->orWhere(
                            "title_en",
                            "like",
                            "%{$search}%",
                        );
                    });
                })
                ->paginate(9);

            return response()->json(
                [
                    "message" => "Media fetched successfully.",
                    "data" => $media,
                    "existingCategory" => $existingCategory,
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error fetching media: " . $e->getMessage());
            return response()->json(
                [
                    "message" => "Failed to fetch media.",
                ],
                500,
            );
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "title_id" => "required|string|max:255",
            "title_en" => "required|string|max:255",
            "description_id" => "required|string",
            "description_en" => "required|string",
            "slug_id" => "required|string|unique:media,slug_id",
            "slug_en" => "required|string|unique:media,slug_en",
            "category" => ["required", Rule::in(ContentCategory::values())],

            // Validasi untuk file upload
            "image" => "required|array",
            "image.*" => "required|image|mimes:jpeg,png,jpg,webp|max:2048",
        ]);

        try {
            $imagePaths = [];

            if ($request->hasFile("image")) {
                foreach ($request->file("image") as $file) {
                    $path = $file->store("media", "public");
                    $imagePaths[] = "/storage/" . $path;
                }
            }

            $data["image"] = $imagePaths;

            $media = Media::create($data);

            return response()->json(
                [
                    "message" => "Media created successfully.",
                    "data" => $media,
                ],
                201,
            );
        } catch (\Throwable $e) {
            Log::error("Error creating media: " . $e->getMessage());
            return response()->json(
                [
                    "message" => "Failed to create media.",
                ],
                500,
            );
        }
    }

    public function show($id)
    {
        try {
            $media = Media::findOrFail($id);

            return response()->json(
                [
                    "message" => "Media fetched successfully.",
                    "data" => $media,
                ],
                200,
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    "message" => "Media not found.",
                ],
                404,
            );
        } catch (\Throwable $e) {
            Log::error("Error fetching media detail: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Failed to fetch media detail. Please try again later.",
                ],
                500,
            );
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "title_id" => "sometimes|required|string|max:255",
            "title_en" => "sometimes|required|string|max:255",
            "description_id" => "sometimes|required|string",
            "description_en" => "sometimes|required|string",
            "slug_id" => [
                "sometimes",
                "string",
                Rule::unique("media")->ignore($id),
            ],
            "slug_en" => [
                "sometimes",
                "string",
                Rule::unique("media")->ignore($id),
            ],
            "category" => [
                "sometimes",
                "required",
                Rule::in(ContentCategory::values()),
            ],

            // Validasi file opsional saat update
            "image" => "sometimes|required|array",
            "image.*" => "image|mimes:jpeg,png,jpg,webp|max:2048",
        ]);

        try {
            $media = Media::findOrFail($id);

            if ($request->hasFile("image")) {
                // Hapus gambar lama dari storage server terlebih dahulu
                if (is_array($media->image)) {
                    foreach ($media->image as $oldImage) {
                        $oldPath = str_replace("/storage/", "", $oldImage);
                        Storage::disk("public")->delete($oldPath);
                    }
                }

                // Upload gambar baru
                $newImagePaths = [];
                foreach ($request->file("image") as $file) {
                    $path = $file->store("media", "public");
                    $newImagePaths[] = "/storage/" . $path;
                }
                $validated["image"] = $newImagePaths;
            }

            $media->update($validated);

            return response()->json(
                [
                    "message" => "Media updated successfully.",
                    "data" => $media,
                ],
                200,
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    "message" => "Media not found.",
                    "data" => null,
                ],
                404,
            );
        } catch (\Throwable $e) {
            Log::error("Error updating media: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Failed to update media. Please try again later.",
                    "data" => null,
                ],
                500,
            );
        }
    }

    public function destroy($id)
    {
        try {
            $media = Media::findOrFail($id);

            // Hapus semua file gambar yang terkait dengan media ini dari storage
            if (is_array($media->image)) {
                foreach ($media->image as $oldImage) {
                    $oldPath = str_replace("/storage/", "", $oldImage);
                    Storage::disk("public")->delete($oldPath);
                }
            }

            $media->delete();

            return response()->json(
                [
                    "message" => "Media deleted successfully.",
                    "data" => null,
                ],
                200,
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    "message" => "Media not found.",
                    "data" => null,
                ],
                404,
            );
        } catch (\Throwable $e) {
            Log::error("Error deleting media: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Failed to delete media. Please try again later.",
                    "data" => null,
                ],
                500,
            );
        }
    }
}
