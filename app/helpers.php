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
        $routeName = request()->route()?->getName();

        if (is_string($routeName) && Str::startsWith($routeName, 'customer.')) {
            return [
                'email' => 'minhan@gmail.com',
                'name' => 'Nguyen Minh An',
                'role' => 'customer',
                'role_label' => 'Customer Preview',
            ];
        }

        if (is_string($routeName) && Str::startsWith($routeName, 'reception.')) {
            return [
                'email' => 'reception.preview@peachvalley.test',
                'name' => 'Le tan demo',
                'role' => 'receptionist',
                'role_label' => 'Reception Preview',
            ];
        }

        if (
            is_string($routeName)
            && (
                Str::startsWith($routeName, 'hotel.')
                || in_array($routeName, ['admin.dashboard', 'pages.term-of-use'], true)
            )
        ) {
            return [
                'email' => 'manager.preview@peachvalley.test',
                'name' => 'Quan ly demo',
                'role' => 'manager',
                'role_label' => 'Admin Preview',
            ];
        }

        return null;
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
        return (string) (mockUser()['name'] ?? 'Khach');
    }
}

if (! function_exists('currentUserRoleLabel')) {
    function currentUserRoleLabel(): string
    {
        return (string) (mockUser()['role_label'] ?? 'Khach');
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

        return route('customer.home');
    }
}
