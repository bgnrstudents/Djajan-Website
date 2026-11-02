<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        $valid = match ($role) {
            'pelanggan' => $user instanceof \App\Models\Pelanggan,
            'umkm' => $user instanceof \App\Models\Umkm,
            default => false,
        };

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak memiliki akses ke endpoint ini.',
            ], 403);
        }

        return $next($request);
    }
}
