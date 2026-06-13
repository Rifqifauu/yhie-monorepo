<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query("search");
        $category = $request->query("category");

        try {
            $articles = Article::with("author")
                ->orderBy("created_at", "desc")
                ->orderBy("id", "desc")
                ->when($category, function ($query, $category) {
                    return $query->where("category", $category);
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
                ->paginate($request->query("per_page", 9));

            return response()->json(
                [
                    "message" => "Articles fetched successfully.",
                    "data" => $articles,
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error fetching articles: " . $e->getMessage());
            return response()->json(
                [
                    "message" => "Failed to fetch articles.",
                ],
                500,
            );
        }
    }

    public function store(Request $request)
    {
        $rules = [
            "title_id" => "required|string|max:255",
            "title_en" => "required|string|max:255",
            "content_id" => "required|string",
            "content_en" => "required|string",
            "slug_id" => "nullable|string|max:255",
            "slug_en" => "nullable|string|max:255",
            "is_published" => "required|boolean",
            "author_id" => "nullable|integer|exists:users,id",
            "category" => "required|string|max:255",
        ];

        if ($request->hasFile("image")) {
            $rules["image"] = "required|array";
            $rules["image.*"] =
                "required|image|mimes:jpeg,png,jpg,webp|max:2048";
        } else {
            $rules["image"] = "nullable|array";
            $rules["image.*"] = "nullable|string|max:255";
        }

        $data = $request->validate($rules);

        try {
            if (empty($data["slug_id"])) {
                $data["slug_id"] = Str::slug($data["title_id"]);
            }
            if (empty($data["slug_en"])) {
                $data["slug_en"] = Str::slug($data["title_en"]);
            }

            $data["author_id"] = $data["author_id"] ?? (auth()->id() ?? 1);
            $data["image"] = $this->storeImages($request);

            $article = Article::create($data);

            return response()->json(
                [
                    "message" => "Article created successfully.",
                    "data" => $article->fresh("author"),
                ],
                201,
            );
        } catch (\Throwable $e) {
            Log::error("Error creating article: " . $e->getMessage());
            return response()->json(
                [
                    "message" => "Failed to create article.",
                ],
                500,
            );
        }
    }

    public function show($slug)
    {
        try {
            $article = Article::with("author")
                ->where("slug_id", $slug)
                ->orWhere("slug_en", $slug)
                ->firstOrFail();

            return response()->json(
                [
                    "message" => "Article fetched successfully.",
                    "data" => $article,
                ],
                200,
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    "message" => "Article not found.",
                ],
                404,
            );
        } catch (\Throwable $e) {
            Log::error("Error fetching article detail: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Failed to fetch article detail. Please try again later.",
                ],
                500,
            );
        }
    }

    public function update(Request $request, $id)
    {
        $rules = [
            "title_id" => "sometimes|required|string|max:255",
            "title_en" => "sometimes|required|string|max:255",
            "content_id" => "sometimes|required|string",
            "content_en" => "sometimes|required|string",
            "slug_id" => "nullable|string|max:255",
            "slug_en" => "nullable|string|max:255",
            "is_published" => "sometimes|required|boolean",
            "author_id" => "sometimes|nullable|integer|exists:users,id",
            "category" => "sometimes|required|string|max:255",
        ];

        if ($request->hasFile("image")) {
            $rules["image"] = "sometimes|required|array";
            $rules["image.*"] =
                "required|image|mimes:jpeg,png,jpg,webp|max:2048";
        } elseif ($request->has("image")) {
            $rules["image"] = "sometimes|nullable|array";
            $rules["image.*"] = "nullable|string|max:255";
        }

        $validated = $request->validate($rules);

        try {
            $article = Article::findOrFail($id);

            if ($request->hasFile("image")) {
                $this->deleteImages($article->image);
                $validated["image"] = $this->storeImages($request);
            } elseif ($request->boolean("remove_images")) {
                $this->deleteImages($article->image);
                $validated["image"] = [];
            }

            if (
                array_key_exists("slug_id", $validated) &&
                empty($validated["slug_id"]) &&
                isset($validated["title_id"])
            ) {
                $validated["slug_id"] = Str::slug($validated["title_id"]);
            }
            if (
                array_key_exists("slug_en", $validated) &&
                empty($validated["slug_en"]) &&
                isset($validated["title_en"])
            ) {
                $validated["slug_en"] = Str::slug($validated["title_en"]);
            }

            if (
                array_key_exists("author_id", $validated) &&
                empty($validated["author_id"])
            ) {
                $validated["author_id"] = $article->author_id;
            }

            $article->update($validated);

            return response()->json(
                [
                    "message" => "Article updated successfully.",
                    "data" => $article->fresh("author"),
                ],
                200,
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    "message" => "Article not found.",
                    "data" => null,
                ],
                404,
            );
        } catch (\Throwable $e) {
            Log::error("Error updating article: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Failed to update article. Please try again later.",
                    "data" => null,
                ],
                500,
            );
        }
    }

    public function destroy($id)
    {
        try {
            $article = Article::findOrFail($id);

            if (is_array($article->image)) {
                foreach ($article->image as $oldImage) {
                    $oldPath = str_replace("/storage/", "", $oldImage);
                    Storage::disk("public")->delete($oldPath);
                }
            }

            $article->delete();

            return response()->json(
                [
                    "message" => "Article deleted successfully.",
                    "data" => null,
                ],
                200,
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    "message" => "Article not found.",
                    "data" => null,
                ],
                404,
            );
        } catch (\Throwable $e) {
            Log::error("Error deleting article: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Failed to delete article. Please try again later.",
                    "data" => null,
                ],
                500,
            );
        }
    }

    private function storeImages(Request $request): array
    {
        if (!$request->hasFile("image")) {
            return [];
        }

        return collect($request->file("image"))
            ->map(function ($file) {
                return "/storage/" . $file->store("articles", "public");
            })
            ->values()
            ->all();
    }

    private function deleteImages($images): void
    {
        if (!is_array($images)) {
            return;
        }

        foreach ($images as $image) {
            $oldPath = str_replace("/storage/", "", $image);
            Storage::disk("public")->delete($oldPath);
        }
    }
}
