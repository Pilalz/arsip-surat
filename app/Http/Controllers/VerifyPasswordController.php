<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VerifyPasswordController extends Controller
{
    /**
     * Verifikasi password user yang sedang login.
     * Digunakan sebagai "gate" sebelum membuka file PDF sensitif.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum login.',
            ], 401);
        }

        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Password salah. Silakan coba lagi.',
        ], 422);
    }
}
