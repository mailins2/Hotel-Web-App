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
        $allowedRoles = array_map('intval', $roles);

        if (!$authAccount) {
            $authAccount = $this->resolveStoredAccountForRoles($request, $allowedRoles);
        }

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

        $accountRole = (int) ($authAccount['LoaiTaiKhoan'] ?? -1);

        if ($allowedRoles !== [] && !in_array($accountRole, $allowedRoles, true)) {
            $authAccount = $this->resolveStoredAccountForRoles($request, $allowedRoles);
            $accountRole = (int) ($authAccount['LoaiTaiKhoan'] ?? -1);

            if (!in_array($accountRole, $allowedRoles, true)) {
                abort(403);
            }
        }

        return $next($request);
    }

    private function resolveStoredAccountForRoles(Request $request, array $allowedRoles): ?array
    {
        foreach ($allowedRoles as $role) {
            $account = $request->session()->get('auth_accounts.' . $role);

            if (is_array($account) && (int) ($account['LoaiTaiKhoan'] ?? -1) === $role) {
                $request->session()->put('auth_account', $account);

                return $account;
            }
        }

        return null;
    }
}
