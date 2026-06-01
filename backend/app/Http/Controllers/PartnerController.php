<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        $rules = [
            'name_id'        => 'required|string|max:255',
            'description_id' => 'required|string',
            'name_en'        => 'required|string|max:255',
            'description_en' => 'required|string',
            'slug_id'        => 'nullable|string|max:255',
            'slug_en'        => 'nullable|string|max:255',
        ];

        if ($request->hasFile('logo')) {
            $rules['logo'] = 'required|image|mimes:jpeg,png,jpg,webp|max:2048';
        } else {
            $rules['logo'] = 'required|string|max:255';
        }

        $data = $request->validate($rules);

        try {
            if ($request->hasFile('logo')) {
                $data['logo'] = $this->storePartnerLogo($request->file('logo'));
            }

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
        $rules = [
            'name_id'        => 'sometimes|required|string|max:255',
            'description_id' => 'sometimes|required|string',
            'name_en'        => 'sometimes|required|string|max:255',
            'description_en' => 'sometimes|required|string',
            'slug_id'        => 'nullable|string|max:255',
            'slug_en'        => 'nullable|string|max:255',
        ];

        if ($request->hasFile('logo')) {
            $rules['logo'] = 'sometimes|required|image|mimes:jpeg,png,jpg,webp|max:2048';
        } elseif ($request->has('logo')) {
            $rules['logo'] = 'sometimes|required|string|max:255';
        }

        $validated = $request->validate($rules);

        try {
            $partner = Partner::findOrFail($id);

            if ($request->hasFile('logo')) {
                $validated['logo'] = $this->storePartnerLogo($request->file('logo'), $partner);
            }

            if (isset($validated['name_id']) && !isset($validated['slug_id'])) {
                $validated['slug_id'] = Str::slug($validated['name_id']);
            }

            $partner->update($validated);

            return response()->json([
                'message' => 'Partner updated successfully',
                'data'    => $partner
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Partner not found',
                'data'    => null
            ], 404);

        } catch (\Throwable $e) {
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
            $partner = Partner::findOrFail($id);

            $this->deleteStoredLogo($partner->logo);

            $partner->delete();

            return response()->json([
                'message' => 'Partner deleted successfully',
                'data'    => null
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Partner not found',
                'data'    => null
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error deleting partner: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete partner. Please try again later.',
                'data'    => null
            ], 500);
        }
    }

    private function storePartnerLogo($logoFile, ?Partner $partner = null): string
    {
        if ($partner !== null) {
            $this->deleteStoredLogo($partner->logo);
        }

        $path = $logoFile->store('partners', 'public');

        return '/storage/' . $path;
    }

    private function deleteStoredLogo(?string $logoPath): void
    {
        if (empty($logoPath) || !str_starts_with($logoPath, '/storage/')) {
            return;
        }

        $path = str_replace('/storage/', '', $logoPath);
        Storage::disk('public')->delete($path);
    }
}

