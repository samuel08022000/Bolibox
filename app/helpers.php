<?php

if (!function_exists('base_uri')) {
    function base_uri(): string {
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $base = str_replace('/public', '', $dir);
        return rtrim($base, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return base_uri() . '/public/' . ltrim($path, '/');
    }
}


if (!function_exists('url')) {
    function url(string $path = ''): string {
        return base_uri() . '/' . ltrim($path, '/');
    }
}