<?php

// Use Composer autoloader when available, otherwise fall back to a small PSR-4-like loader
$composer = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composer)) {
    require $composer;
    return;
}

spl_autoload_register(function ($class) {
    $prefixes = [
        'Core\\' => __DIR__ . '/',
        'Modules\\' => __DIR__ . '/../modules/'
    ];

    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relative = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative) . '.php';

            if (file_exists($file)) {
                require $file;
                return;
            }

            // Fallback: try a case-insensitive resolution of the path components.
            // This helps when files were uploaded with different casing (Windows vs Linux hosts).
            $segments = explode('/', str_replace('\\', '/', $relative));
            $cur = rtrim($base_dir, '/') . '/';
            $found = true;
            foreach ($segments as $i => $seg) {
                $entries = @scandir($cur);
                if ($entries === false) { $found = false; break; }
                $matched = null;
                foreach ($entries as $e) {
                    if (strcasecmp($e, $seg) === 0) {
                        $matched = $e;
                        break;
                    }
                }
                if ($matched === null) { $found = false; break; }
                $cur = $cur . $matched . '/';
            }
            if ($found) {
                // remove trailing slash and add .php
                $candidate = rtrim($cur, '/') . '.php';
                if (file_exists($candidate)) {
                    require $candidate;
                    return;
                }
            }
            return;
        }
    }
});
