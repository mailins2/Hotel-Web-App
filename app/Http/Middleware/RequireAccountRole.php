<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAccountRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $authAccount = $request->session()->get('auth_account');

        if (!$authAccount) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập.',
                ], 401);
            }

            return redirect()->route('login', [
                'redirect' => $request->fullUrl(),
            ]);
        }

        $allowedRoles = array_map('intval', $roles);
        $accountRole = (int) ($authAccount['LoaiTaiKhoan'] ?? -1);

        if ($allowedRoles !== [] && !in_array($accountRole, $allowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
