<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginGateController extends Controller
{
    /**
     * GET /api/login-gate/status
     * Publik - beri tahu apakah gerbang passcode aktif, TANPA membocorkan nilainya.
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'enabled' => filled(Setting::get('login_passcode', '')),
        ]);
    }

    /**
     * POST /api/login-gate/verify
     * Publik, dibatasi rate limit - cek passcode ke halaman /login.
     * Kosong atau salah = 403. Tidak pernah mengirim nilai asli ke client.
     */
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'passcode' => 'nullable|string',
        ]);

        $expected = Setting::get('login_passcode', '');
        $submitted = $data['passcode'] ?? '';

        if (blank($expected) || blank($submitted) || !hash_equals($expected, $submitted)) {
            return response()->json(['message' => 'Passcode salah.'], 403);
        }

        return response()->json(['message' => 'Passcode valid.'], 200);
    }
}
