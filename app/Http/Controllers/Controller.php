<?php

namespace App\Http\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;

abstract class Controller
{
    protected function currentMockUser(): ?array
    {
        $user = session('mock_auth');

        return is_array($user) ? $user : null;
    }

    protected function requireMockAuthentication(): array
    {
        $user = $this->currentMockUser();

        if ($user === null) {
            throw new HttpResponseException(
                redirect()->route('login')
            );
        }

        return $user;
    }

    protected function requireManagerRole(): array
    {
        $user = $this->requireMockAuthentication();

        if (($user['role'] ?? null) !== 'manager') {
            throw new HttpResponseException(
                redirect()->route('dashboard')->with('status', 'Tài khoản hiện tại không có quyền vào khu quản lý.')
            );
        }

        return $user;
    }

    protected function requireReceptionistRole(): array
    {
        $user = $this->requireMockAuthentication();

        if (($user['role'] ?? null) !== 'receptionist') {
            throw new HttpResponseException(
                redirect()->route('dashboard')->with('status', 'Tài khoản hiện tại không có quyền vào khu lễ tân.')
            );
        }

        return $user;
    }
}
