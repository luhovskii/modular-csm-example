<?php

// Use Composer autoloader when available, otherwise fall back to a small PSR-4-like loader
$composer = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composer)) {
    require $composer;
    return;
}

spl_autoload_register(function ($class) {
    // Lightweight debug: when troubleshooting gallery handler misses,
    // write attempts to storage/autoload_debug.log if possible.
    $debugFor = 'Modules\\Gallery';
    $logPath = __DIR__ . '/../storage/autoload_debug.log';
    if (strpos($class, $debugFor) === 0) {
        @mkdir(dirname($logPath), 0755, true);
        $entry = ['time' => date('c'), 'class' => $class];
        @file_put_contents($logPath, json_encode($entry, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    // Start of autoloading process
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
            // Attempting to locate the file with case-insensitive matching
            $segments = explode('/', str_replace('\\', '/', $relative));
            $cur = rtrim($base_dir, '/') . '/';
            $found = true;
            foreach ($segments as $i => $seg) {
                $entries = @scandir($cur);
                if ($entries === false) {
                    $found = false;
                    break;
                }
                $matched = null;
                foreach ($entries as $e) {
                    if (strcasecmp($e, $seg) === 0) {
                        $matched = $e;
                        break;
                    }
                }
                if ($matched === null) {
                    $found = false;
                    break;
                }
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

            // Final fallback: try to find a file with the same class basename
            // anywhere under the base_dir (case-insensitive). This handles
            // situations where directory scanning is blocked or casing is
            // inconsistent after uploads to a case-sensitive host.
            try {
                $segmentsCount = count($segments);
                $basename = $segments[$segmentsCount - 1];
                $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir, RecursiveDirectoryIterator::SKIP_DOTS));
                foreach ($it as $f) {
                    if ($f->isFile()) {
                        $ext = pathinfo($f->getFilename(), PATHINFO_EXTENSION);
                        if (strtolower($ext) !== 'php') continue;
                        $name = pathinfo($f->getFilename(), PATHINFO_FILENAME);
                        if (strcasecmp($name, $basename) === 0) {
                            require $f->getPathname();
                            return;
                        }
                    }
                }
            } catch (Exception $e) {
                // ignore any filesystem traversal errors and fall through
            }

            return;
        }
    }
});
