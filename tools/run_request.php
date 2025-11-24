<?php
// Usage: php tools/run_request.php "/path"
$uri = $argv[1] ?? '/';
// Simulate server variables
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = $uri;

require __DIR__ . '/../public/index.php';
