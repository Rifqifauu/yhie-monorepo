<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;
class ProgramController extends Controller
{
    // Gunakan try catch untuk menangani error saat mengambil data program
    public function index(Request $request)
    {
        // Gunakan parameter request untuk mendapatkan data dari query string
        try {
            // 1. Ambil keyword pencarian dari input (misal nama input di form-nya adalah 'search')
            $search = $request->query("search");
            // 2. Ambil data program dari database dengan filter berdasarkan keyword pencarian
            $programs = Program::query()
                ->when($search, function ($query, $search) {
                    return $query
                        ->where("title_id", "like", "%" . $search . "%")
                        ->orWhere("title_en", "like", "%" . $search . "%")
                        ->orWhere("description_id", "like", "%" . $search . "%")
                        ->orWhere(
                            "description_en",
                            "like",
                            "%" . $search . "%",
                        );
                })
                ->paginate(10)
                ->withQueryString();
            return response()->json(
                [
                    "message" => "Programs retrieved successfully",
                    "data" => $programs,
                ],
                200,
            );
            // Payload atau format response return response()->json (["message", "data"],code)
            // Sesuaikan status code, 200 untuk success, 404 untuk not found, 500 untuk error server/db
        } catch (Exception $e) {
            Log::error($e->getMessage());
            // Log error dan kembalikan response error
            return response()->json(
                ["message" => "An error occurred while retrieving programs"],
                500,
            );
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title_id" => "required|string|max:255",
            "description_id" => "required|string",
            "title_en" => "required|string|max:255",
            "description_en" => "required|string",
            "image_path" => "required|string|max:255",
            "price_id" => "required|numeric",
            "price_en" => "required|numeric",
            "slug_id" => "nullable|string|max:255",
            "slug_en" => "nullable|string|max:255",
        ]);

        if (empty($validated["slug_id"])) {
            $validated["slug_id"] = Str::slug($validated["title_id"]);
        }
        if (empty($validated["slug_en"])) {
            $validated["slug_en"] = Str::slug($validated["title_en"]);
        }

        $program = Program::create($validated);

        return response()->json(
            [
                "success" => true,
                "message" => "Program created successfully",
                "data" => $program,
            ],
            201,
        );
    }

    public function show($id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Program not found",
                    "data" => null,
                ],
                404,
            );
        }

        return response()->json([
            "success" => true,
            "message" => "Program retrieved successfully",
            "data" => $program,
        ]);
    }

    public function update(Request $request, $id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Program not found",
                    "data" => null,
                ],
                404,
            );
        }

        $validated = $request->validate([
            "title_id" => "sometimes|required|string|max:255",
            "description_id" => "sometimes|required|string",
            "title_en" => "sometimes|required|string|max:255",
            "description_en" => "sometimes|required|string",
            "image_path" => "sometimes|required|string|max:255",
            "price_id" => "sometimes|required|numeric",
            "price_en" => "sometimes|required|numeric",
            "slug_id" => "nullable|string|max:255",
            "slug_en" => "nullable|string|max:255",
        ]);

        if (isset($validated["title_id"]) && empty($validated["slug_id"])) {
            $validated["slug_id"] = Str::slug($validated["title_id"]);
        }
        if (isset($validated["title_en"]) && empty($validated["slug_en"])) {
            $validated["slug_en"] = Str::slug($validated["title_en"]);
        }

        $program->update($validated);

        return response()->json([
            "success" => true,
            "message" => "Program updated successfully",
            "data" => $program,
        ]);
    }

    public function destroy($id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Program not found",
                    "data" => null,
                ],
                404,
            );
        }

        $program->delete();

        return response()->json([
            "success" => true,
            "message" => "Program deleted successfully",
            "data" => null,
        ]);
    }
}
