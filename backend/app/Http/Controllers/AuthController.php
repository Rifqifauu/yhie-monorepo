<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * POST /api/register
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:8|confirmed", // butuh password_confirmation di request
        ]);

        try {
            $user = User::create([
                "name" => $validated["name"],
                "email" => $validated["email"],
                "password" => Hash::make($validated["password"]),
            ]);

            // Opsional: Langsung login setelah register
            // Auth::login($user);

            return response()->json(
                [
                    "message" => "User registered successfully.",
                    "data" => $user,
                ],
                201,
            );
        } catch (\Throwable $e) {
            Log::error("Registration error: " . $e->getMessage());

            return response()->json(
                [
                    "message" =>
                        "Failed to register user. Please try again later.",
                ],
                500,
            );
        }
    }

    /**
     * POST /api/login
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            "email" => "required|string|email",
            "password" => "required|string",
        ]);

        try {
            // Auth::attempt akan mengecek email & password.
            // Jika benar, Laravel otomatis membuat session berbasis cookie.
            if (Auth::attempt($credentials)) {
                // Mencegah session fixation attack
                $request->session()->regenerate();

                return response()->json(
                    [
                        "message" => "Logged in successfully.",
                        "data" => Auth::user(),
                    ],
                    200,
                );
            }

            // Jika gagal login
            return response()->json(
                [
                    "message" =>
                        "Kredensial tidak valid. Email atau password salah.",
                ],
                401,
            );
        } catch (\Throwable $e) {
            Log::error("Login error: " . $e->getMessage());

            return response()->json(
                [
                    "message" =>
                        "An error occurred during login. Please try again later.",
                ],
                500,
            );
        }
    }

    /**
     * PUT /api/account
     * Update profil akun admin yang sedang login (nama, email, password).
     */
    public function updateAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            "name" => "sometimes|string|max:255",
            "email" => "sometimes|string|email|max:255|unique:users,email," . $user->id,
            "current_password" => "required_with:password|string",
            "password" => "sometimes|string|min:8|confirmed",
        ]);

        if (isset($data["password"])) {
            if (!Hash::check($data["current_password"] ?? "", $user->password)) {
                return response()->json(
                    ["message" => "Password saat ini tidak sesuai."],
                    422,
                );
            }
            $data["password"] = Hash::make($data["password"]);
        }
        unset($data["current_password"]);

        try {
            $user->update($data);

            return response()->json(
                [
                    "message" => "Akun berhasil diperbarui.",
                    "data" => $user->fresh(),
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error updating admin account: " . $e->getMessage());

            return response()->json(
                ["message" => "Gagal memperbarui akun. Silakan coba lagi."],
                500,
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            // Melakukan proses logout
            Auth::guard("web")->logout();

            // Menghapus session saat ini
            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return response()->json(
                [
                    "message" => "Logged out successfully.",
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Logout error: " . $e->getMessage());

            return response()->json(
                [
                    "message" => "An error occurred during logout.",
                ],
                500,
            );
        }
    }
}
