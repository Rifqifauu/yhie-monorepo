<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('author')->get();
        return response()->json([
            'success' => true,
            'message' => 'Articles retrieved successfully',
            'data' => $articles
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_id' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'content_id' => 'required|string',
            'content_en' => 'required|string',
            'image' => 'nullable|array',
            'slug_id' => 'nullable|string|max:255',
            'slug_en' => 'nullable|string|max:255',
            'is_published' => 'required|boolean',
            'author_id' => 'required|exists:users,id',
            'category' => 'required|string|max:255',
        ]);

        if (empty($validated['slug_id'])) {
            $validated['slug_id'] = Str::slug($validated['title_id']);
        }
        if (empty($validated['slug_en'])) {
            $validated['slug_en'] = Str::slug($validated['title_en']);
        }

        $article = Article::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully',
            'data' => $article
        ], 201);
    }

    public function show($id)
    {
        $article = Article::with('author')->find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Article retrieved successfully',
            'data' => $article
        ]);
    }

    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'title_id' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'content_id' => 'sometimes|required|string',
            'content_en' => 'sometimes|required|string',
            'image' => 'nullable|array',
            'slug_id' => 'nullable|string|max:255',
            'slug_en' => 'nullable|string|max:255',
            'is_published' => 'sometimes|required|boolean',
            'author_id' => 'sometimes|required|exists:users,id',
            'category' => 'sometimes|required|string|max:255',
        ]);

        if (isset($validated['title_id']) && empty($validated['slug_id'])) {
            $validated['slug_id'] = Str::slug($validated['title_id']);
        }
        if (isset($validated['title_en']) && empty($validated['slug_en'])) {
            $validated['slug_en'] = Str::slug($validated['title_en']);
        }

        $article->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully',
            'data' => $article
        ]);
    }

    public function destroy($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
                'data' => null
            ], 404);
        }

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully',
            'data' => null
        ]);
    }
}
