<?php

namespace App\Http\Controllers;

use App\Models\ProgramRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProgramRegistrationController extends Controller
{
    // ── PUBLIC ────────────────────────────────────────────────────────────────

    /**
     * POST /api/program-registrations
     * Public endpoint — tidak memerlukan auth.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:30',
            'gender'     => 'required|in:male,female',
            'age'        => 'nullable|integer|min:1|max:150',
            'address'    => 'nullable|string|max:1000',
            'notes'      => 'nullable|string|max:1000',
        ]);

        try {
            $registration = ProgramRegistration::create($data);

            return response()->json([
                'message' => 'Registration submitted successfully.',
                'data'    => $registration->load('program'),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Error creating program registration: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to submit registration. Please try again later.',
            ], 500);
        }
    }

    // ── ADMIN ─────────────────────────────────────────────────────────────────

    /**
     * GET /api/program-registrations
     * Daftar semua pendaftaran (paginated, searchable).
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');

        try {
            $registrations = ProgramRegistration::with('program')
                ->orderBy('created_at', 'desc')
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })->paginate(15);

            return response()->json([
                'message' => 'Registrations fetched successfully.',
                'data'    => $registrations,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error fetching registrations: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch registrations.',
            ], 500);
        }
    }

    /**
     * GET /api/program-registrations/{registration}
     */
    public function show(ProgramRegistration $programRegistration): JsonResponse
    {
        return response()->json([
            'message' => 'Registration fetched successfully.',
            'data'    => $programRegistration->load('program'),
        ], 200);
    }

    /**
     * DELETE /api/program-registrations/{registration}
     */
    public function destroy(ProgramRegistration $programRegistration): JsonResponse
    {
        try {
            $programRegistration->delete();

            return response()->json([
                'message' => 'Registration deleted successfully.',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error deleting registration: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete registration.',
            ], 500);
        }
    }
}
