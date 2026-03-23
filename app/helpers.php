<?php
// Detectar la ruta base automáticamente
if (!function_exists('base_uri')) {
    function base_uri(): string {
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $base = str_replace('/public', '', $dir);
        return rtrim($base, '/');
    }
}

// Para cargar CSS, JS e imágenes desde la carpeta /public
if (!function_exists('asset')) {
    function asset(string $path): string {
        return base_uri() . '/public/' . ltrim($path, '/');
    }
}

// Para crear enlaces entre páginas (ej: url('registro'))
if (!function_exists('url')) {
    function url(string $path = ''): string {
        return base_uri() . '/' . ltrim($path, '/');
    }
}