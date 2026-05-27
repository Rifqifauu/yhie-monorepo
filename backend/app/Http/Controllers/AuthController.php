<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                "email" => "required|email",
                "password" => "required",
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                $user = Auth::user();
                return response()->json(
                    [
                        "message" => "Login berhasil",
                        "data" => $user,
                    ],
                    200,
                );
            }

            return response()->json(
                [
                    "message" => "Email atau password salah.",
                ],
                401,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    "message" => "Validasi gagal. " . $e->getMessage(),
                ],
                422,
            );
        } catch (\Exception $e) {
            Log::error("Login Error System: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Terjadi kesalahan pada server saat proses login." .
                        $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::guard("web")->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(["message" => "Berhasil logout"], 200);
        } catch (\Exception $e) {
            Log::error("Logout Error: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Terjadi kesalahan pada server saat proses logout." .
                        $e->getMessage(),
                ],
                500,
            );
        }
    }
}
