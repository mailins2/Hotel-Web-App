<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MockAuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (isMockAuthenticated()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login', [
            'demoUsers' => config('hotel-management.demo_auth.users', []),
            'demoPassword' => config('hotel-management.demo_auth.password', '123456'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'role' => ['nullable', 'in:manager,receptionist'],
        ]);

        $demoUsers = collect(config('hotel-management.demo_auth.users', []));
        $email = mb_strtolower(trim((string) $credentials['email']));
        $role = $credentials['role'] ?? null;

        $matchedUser = $demoUsers->first(function (array $user) use ($email, $role) {
            $userEmail = mb_strtolower(trim((string) ($user['email'] ?? '')));

            if ($userEmail !== $email) {
                return false;
            }

            if ($role !== null && $role !== '' && ($user['role'] ?? null) !== $role) {
                return false;
            }

            return true;
        });

        if (! is_array($matchedUser)) {
            return back()
                ->withErrors(['email' => 'Email không đúng hoặc tài khoản không tồn tại.'])
                ->withInput($request->except('password'));
        }

        $expectedPassword = (string) ($matchedUser['password'] ?? config('hotel-management.demo_auth.password', '123456'));

        if ($credentials['password'] !== $expectedPassword) {
            return back()
                ->withErrors(['password' => 'Mật khẩu không đúng.'])
                ->withInput($request->except('password'));
        }

        if ((int) ($matchedUser['status'] ?? 1) !== 1) {
            return back()
                ->withErrors(['email' => 'Tài khoản demo này đang bị khóa.'])
                ->withInput($request->except('password'));
        }

        $this->loginDemoUser($request, $matchedUser);

        return redirect()->route('dashboard');
    }

    public function google(Request $request): RedirectResponse
    {
        $demoUser = collect(config('hotel-management.demo_auth.users', []))
            ->first(fn (array $user) => (int) ($user['status'] ?? 1) === 1);

        if (! is_array($demoUser)) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Chưa có tài khoản demo khả dụng để đăng nhập bằng Google.']);
        }

        $this->loginDemoUser($request, $demoUser);

        return redirect()->route('dashboard');
    }

    public function dashboardRedirect(): RedirectResponse
    {
        $user = $this->requireMockAuthentication();

        return match ($user['role'] ?? null) {
            'receptionist' => redirect()->route('reception.dashboard'),
            default => redirect()->route('admin.dashboard'),
        };
    }

    protected function loginDemoUser(Request $request, array $matchedUser): void
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->regenerate();
        $request->session()->put('mock_auth', [
            'email' => $matchedUser['email'],
            'name' => $matchedUser['name'],
            'role' => $matchedUser['role'],
            'role_label' => $matchedUser['role_label'],
        ]);
    }
}
