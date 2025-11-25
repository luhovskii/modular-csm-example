<?php
require __DIR__ . '/../core/bootstrap.php';
$cls = 'Modules\\Gallery\\Controllers\\GalleryController';
var_dump(class_exists($cls));
// show any debug log if present
$log = __DIR__ . '/../storage/autoload_debug.log';
if (file_exists($log)) {
    echo "\n--- autoload_debug.log ---\n";
    echo file_get_contents($log);
}
