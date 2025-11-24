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
            }
            return;
        }
    }
});
