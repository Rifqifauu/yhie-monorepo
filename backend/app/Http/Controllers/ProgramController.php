<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    // ── PUBLIC ────────────────────────────────────────────────────────────────

    /**
     * GET /api/programs
     * Semua program sertifikasi (Hafiz & Maulana).
     */
    public function index(): JsonResponse
    {
        $programs = Program::orderBy('created_at')->get();

        return response()->json($programs);
    }

    /**
     * GET /api/programs/{slug}
     * Detail satu program — slug bisa _id atau _en.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $lang   = $request->query('lang', 'id');
        $column = $lang === 'en' ? 'slug_en' : 'slug_id';

        $program = Program::where($column, $slug)->firstOrFail();

        return response()->json($program);
    }

    // ── ADMIN ─────────────────────────────────────────────────────────────────

    /**
     * POST /api/programs
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title_id'       => 'required|string|max:255',
            'description_id' => 'required|string',
            'title_en'       => 'required|string|max:255',
            'description_en' => 'required|string',
            'image_path'     => 'required|string',
            'price_id'       => 'required|numeric|min:0',
            'price_en'       => 'required|numeric|min:0',
            'slug_id'        => 'required|string|unique:programs,slug_id',
            'slug_en'        => 'required|string|unique:programs,slug_en',
        ]);

        $program = Program::create($data);

        return response()->json($program, 201);
    }

    /**
     * PUT /api/programs/{program}
     */
    public function update(Request $request, Program $program): JsonResponse
    {
        $data = $request->validate([
            'title_id'       => 'sometimes|string|max:255',
            'description_id' => 'sometimes|string',
            'title_en'       => 'sometimes|string|max:255',
            'description_en' => 'sometimes|string',
            'image_path'     => 'sometimes|string',
            'price_id'       => 'sometimes|numeric|min:0',
            'price_en'       => 'sometimes|numeric|min:0',
            'slug_id'        => ['sometimes', 'string', Rule::unique('programs')->ignore($program->id)],
            'slug_en'        => ['sometimes', 'string', Rule::unique('programs')->ignore($program->id)],
        ]);

        $program->update($data);

        return response()->json($program);
    }

    /**
     * DELETE /api/programs/{program}
     */
    public function destroy(Program $program): JsonResponse
    {
        $program->delete();

        return response()->json(['message' => 'Program deleted.'], 200);
    }
}

