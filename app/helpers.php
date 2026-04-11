<?php

use Illuminate\Support\Str;

if (! function_exists('activeRoute')) {
    function activeRoute(?string $url): string
    {
        if (blank($url)) {
            return '';
        }

        $currentUrl = url()->current();

        return Str::startsWith($currentUrl, $url) ? 'active' : '';
    }
}

if (! function_exists('mockUser')) {
    function mockUser(): ?array
    {
        $user = session('mock_auth');

        return is_array($user) ? $user : null;
    }
}

if (! function_exists('isMockAuthenticated')) {
    function isMockAuthenticated(): bool
    {
        return mockUser() !== null;
    }
}

if (! function_exists('currentUserRole')) {
    function currentUserRole(): ?string
    {
        return mockUser()['role'] ?? null;
    }
}

if (! function_exists('isManager')) {
    function isManager(): bool
    {
        return currentUserRole() === 'manager';
    }
}

if (! function_exists('isReceptionist')) {
    function isReceptionist(): bool
    {
        return currentUserRole() === 'receptionist';
    }
}

if (! function_exists('currentUserName')) {
    function currentUserName(): string
    {
        return (string) (mockUser()['name'] ?? 'Khách');
    }
}

if (! function_exists('currentUserRoleLabel')) {
    function currentUserRoleLabel(): string
    {
        return (string) (mockUser()['role_label'] ?? 'Khách');
    }
}

if (! function_exists('portalDashboardRoute')) {
    function portalDashboardRoute(): string
    {
        if (isReceptionist()) {
            return route('reception.dashboard');
        }

        if (isManager()) {
            return route('admin.dashboard');
        }

        return route('login');
    }
}
