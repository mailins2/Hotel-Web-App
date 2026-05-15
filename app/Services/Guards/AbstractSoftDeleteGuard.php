<?php

namespace App\Services\Guards;

abstract class AbstractSoftDeleteGuard
{
    protected function allow(): array
    {
        return [
            'allowed' => true,
            'message' => '',
        ];
    }

    protected function deny(string $message): array
    {
        return [
            'allowed' => false,
            'message' => $message,
        ];
    }
}
