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
        $search = $request->query('search');

        try {
            $articles = Article::with('author')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('title_id', 'like', "%{$search}%")
                            ->orWhere('title_en', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%");
                    });
                })->paginate($request->query('per_page', 10));

            return response()->json([
                'message' => 'Articles fetched successfully.',
                'data' => $articles
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error fetching articles: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch articles.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'title_id' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'content_id' => 'required|string',
            'content_en' => 'required|string',
            'slug_id' => 'nullable|string|max:255',
            'slug_en' => 'nullable|string|max:255',
            'is_published' => 'required|boolean',
            'author_id' => 'required|exists:users,id',
            'category' => 'required|string|max:255',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'required|array';
            $rules['image.*'] = 'required|image|mimes:jpeg,png,jpg,webp|max:2048';
        } else {
            $rules['image'] = 'nullable|array';
            $rules['image.*'] = 'nullable|string|max:255';
        }

        $data = $request->validate($rules);

        try {
            if (empty($data['slug_id'])) {
                $data['slug_id'] = Str::slug($data['title_id']);
            }
            if (empty($data['slug_en'])) {
                $data['slug_en'] = Str::slug($data['title_en']);
            }

            if ($request->hasFile('image')) {
                $imagePaths = [];
                foreach ($request->file('image') as $file) {
                    $path = $file->store('articles', 'public');
                    $imagePaths[] = '/storage/' . $path;
                }
                $data['image'] = $imagePaths;
            } else {
                $data['image'] = $data['image'] ?? [];
            }

            $article = Article::create($data);

            return response()->json([
                'message' => 'Article created successfully.',
                'data' => $article
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Error creating article: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create article.'
            ], 500);
        }
    }

    public function show($slug)
    {
        try {
            $article = Article::where('slug_id', $slug)->orWhere('slug_en', $slug)->first();

            return response()->json([
                'message' => 'Article fetched successfully.',
                'data' => $article
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Article not found.'
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error fetching article detail: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch article detail. Please try again later.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title_id' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'content_id' => 'sometimes|required|string',
            'content_en' => 'sometimes|required|string',
            'slug_id' => 'nullable|string|max:255',
            'slug_en' => 'nullable|string|max:255',
            'is_published' => 'sometimes|required|boolean',
            'author_id' => 'sometimes|required|exists:users,id',
            'category' => 'sometimes|required|string|max:255',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'sometimes|required|array';
            $rules['image.*'] = 'required|image|mimes:jpeg,png,jpg,webp|max:2048';
        } elseif ($request->has('image')) {
            $rules['image'] = 'sometimes|nullable|array';
            $rules['image.*'] = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        try {
            $article = Article::findOrFail($id);

            if ($request->hasFile('image')) {
                // Delete old images
                if (is_array($article->image)) {
                    foreach ($article->image as $oldImage) {
                        $oldPath = str_replace('/storage/', '', $oldImage);
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                // Upload new images
                $newImagePaths = [];
                foreach ($request->file('image') as $file) {
                    $path = $file->store('articles', 'public');
                    $newImagePaths[] = '/storage/' . $path;
                }
                $validated['image'] = $newImagePaths;
            }

            if (isset($validated['title_id']) && empty($validated['slug_id'])) {
                $validated['slug_id'] = Str::slug($validated['title_id']);
            }
            if (isset($validated['title_en']) && empty($validated['slug_en'])) {
                $validated['slug_en'] = Str::slug($validated['title_en']);
            }

            $article->update($validated);

            return response()->json([
                'message' => 'Article updated successfully.',
                'data' => $article
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Article not found.',
                'data' => null
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error updating article: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update article. Please try again later.',
                'data' => null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $article = Article::findOrFail($id);

            // Delete associated images
            if (is_array($article->image)) {
                foreach ($article->image as $oldImage) {
                    $oldPath = str_replace('/storage/', '', $oldImage);
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $article->delete();

            return response()->json([
                'message' => 'Article deleted successfully.',
                'data' => null
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Article not found.',
                'data' => null
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error deleting article: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete article. Please try again later.',
                'data' => null
            ], 500);
        }
    }
}
