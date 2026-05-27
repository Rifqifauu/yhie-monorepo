<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::all();
        return response()->json([
            'success' => true,
            'message' => 'Programs retrieved successfully',
            'data' => $programs
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_id' => 'required|string|max:255',
            'description_id' => 'required|string',
            'title_en' => 'required|string|max:255',
            'description_en' => 'required|string',
            'image_path' => 'required|string|max:255',
            'price_id' => 'required|numeric',
            'price_en' => 'required|numeric',
            'slug_id' => 'nullable|string|max:255',
            'slug_en' => 'nullable|string|max:255',
        ]);

        if (empty($validated['slug_id'])) {
            $validated['slug_id'] = Str::slug($validated['title_id']);
        }
        if (empty($validated['slug_en'])) {
            $validated['slug_en'] = Str::slug($validated['title_en']);
        }

        $program = Program::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Program created successfully',
            'data' => $program
        ], 201);
    }

    public function show($id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Program retrieved successfully',
            'data' => $program
        ]);
    }

    public function update(Request $request, $id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'title_id' => 'sometimes|required|string|max:255',
            'description_id' => 'sometimes|required|string',
            'title_en' => 'sometimes|required|string|max:255',
            'description_en' => 'sometimes|required|string',
            'image_path' => 'sometimes|required|string|max:255',
            'price_id' => 'sometimes|required|numeric',
            'price_en' => 'sometimes|required|numeric',
            'slug_id' => 'nullable|string|max:255',
            'slug_en' => 'nullable|string|max:255',
        ]);

        if (isset($validated['title_id']) && empty($validated['slug_id'])) {
            $validated['slug_id'] = Str::slug($validated['title_id']);
        }
        if (isset($validated['title_en']) && empty($validated['slug_en'])) {
            $validated['slug_en'] = Str::slug($validated['title_en']);
        }

        $program->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Program updated successfully',
            'data' => $program
        ]);
    }

    public function destroy($id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found',
                'data' => null
            ], 404);
        }

        $program->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program deleted successfully',
            'data' => null
        ]);
    }
}
