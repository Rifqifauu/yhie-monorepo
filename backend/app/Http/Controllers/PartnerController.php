<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
    $search = $request->query('search');
    
    try {
        $partners = Partner::orderBy('created_at', 'desc') // Lebih lazim mengurutkan data dari yang terbaru (desc)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name_id', 'like', "%{$search}%")
                      ->orWhere('name_en', 'like', "%{$search}%");
                });
            })->paginate(10);

        return response()->json([
            'message' => 'Partners fetched successfully.',
            'data'    => $partners
        ], 200);

    } catch (\Throwable $e) {
        Log::error('Error fetching programs: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to fetch programs.'
        ], 500);
    }
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_id' => 'required|string|max:255',
            'description_id' => 'required|string',
            'name_en' => 'required|string|max:255',
            'description_en' => 'required|string',
            'slug_id' => 'nullable|string|max:255',
            'slug_en' => 'nullable|string|max:255',
            'logo' => 'required|string|max:255',
        ]);
        try {
            $partner = Partner::create($data);

            return response()->json([
                'message' => 'Partner created successfully.',
                'data'    => $partner
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error creating partner: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create partner.'
            ], 500);
        }
    }

    public function show($id)
    {
     

        try {
            $partner = Partner::findOrFail($id);

            return response()->json([
                'message' => 'Partner fetched successfully.',
                'data'    => $partner
            ], 200);

        } catch (ModelNotFoundException $e) {
            // Tangkap jika firstOrFail() gagal menemukan data
            return response()->json([
                'message' => 'Partner not found.'
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error fetching partner detail: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch partner detail. Please try again later.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name_id' => 'sometimes|required|string|max:255',
        'description_id' => 'sometimes|required|string',
        'name_en' => 'sometimes|required|string|max:255',
        'description_en' => 'sometimes|required|string',
        'slug_id' => 'nullable|string|max:255',
        'slug_en' => 'nullable|string|max:255',
        'logo' => 'sometimes|required|string|max:255',
    ]);

    try {
        
        $partner = Partner::findOrFail($id);
        if (isset($validated['name_id']) && !isset($validated['slug_id'])) {
            $validated['slug_id'] = Str::slug($validated['name_id']);
        }

        // 4. Update data ke database
        $partner->update($validated);

        return response()->json([
            'message' => 'Partner updated successfully',
            'data'    => $partner
        ], 200);

    } catch (ModelNotFoundException $e) {
        // Tangkap error jika ID partner tidak ditemukan
        return response()->json([
            'message' => 'Partner not found',
            'data'    => null
        ], 404);

    } catch (\Throwable $e) {
        // Tangkap error server lainnya (misal database bermasalah)
        Log::error('Error updating partner: ' . $e->getMessage());

        return response()->json([
            'message' => 'Failed to update partner. Please try again later.',
            'data'    => null
        ], 500);
    }
}

   public function destroy($id)
{
    try {
        // Mencari data, jika tidak ada langsung masuk ke catch (ModelNotFoundException)
        $partner = Partner::findOrFail($id);

        $partner->delete();

        return response()->json([
            'message' => 'Partner deleted successfully',
            'data'    => null
        ], 200);

    } catch (ModelNotFoundException $e) {
        // Menangkap error jika ID tidak ditemukan di database
        return response()->json([
            'message' => 'Partner not found',
            'data'    => null
        ], 404);

    } catch (\Throwable $e) {
        // Menangkap error lain (misal: database down atau ada relasi foreing key yang menghalangi)
        Log::error('Error deleting partner: ' . $e->getMessage());

        return response()->json([
            'message' => 'Failed to delete partner. Please try again later.',
            'data'    => null
        ], 500);
    }
}
}
