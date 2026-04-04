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
