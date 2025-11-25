<?php
require_once __DIR__ . '/../core/bootstrap.php';

use Core\Router;
use Core\ModuleManager;
use Core\Auth;
use Core\Csrf;
use Core\Middleware;

Auth::start();

$router = new Router();

// Root: redirect to blog listing
$router->get('/', function () {
    header('Location: /blog');
    exit;
});

// Admin login form
$router->get('/admin/login', function () {
    $csrf = Csrf::getToken();
    $redirect = $_GET['redirect'] ?? '/admin/users';
    // keep only path starting with '/'
    if (!is_string($redirect) || strpos($redirect, '/') !== 0) {
        $redirect = '/admin/users';
    }

    $csrfEsc = htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8');
    $redirectEsc = htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8');

    $html = <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin Login</title>
    <style>
        :root{font-family:system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial;}
        body{margin:0;background:#f5f7fb;color:#222;display:flex;align-items:center;justify-content:center;height:100vh}
        .card{width:100%;max-width:420px;background:#fff;border-radius:8px;box-shadow:0 6px 18px rgba(15,20,30,.08);padding:24px}
        h1{margin:0 0 12px;font-size:20px}
        .field{margin-bottom:12px}
        label{display:block;font-size:13px;color:#444;margin-bottom:6px}
        input[type="text"],input[type="password"]{width:100%;padding:10px 12px;border:1px solid #e3e7ee;border-radius:6px;font-size:14px}
        .actions{display:flex;gap:8px;align-items:center;justify-content:space-between;margin-top:14px}
        button{background:#2563eb;color:#fff;border:none;padding:10px 14px;border-radius:6px;font-weight:600;cursor:pointer}
        .muted{color:#6b7280;font-size:13px}
        .top-link{display:block;text-align:center;margin-bottom:14px;color:#2563eb;text-decoration:none}
        @media (max-width:480px){.card{margin:16px;}}
    </style>
</head>
<body>
    <div class="card" role="main">
        <a class="top-link" href="/">← Back to public site</a>
        <h1>Admin Login</h1>
        <form method="POST" action="/admin/login">
            <input type="hidden" name="csrf_token" value="{$csrfEsc}">
            <input type="hidden" name="redirect" value="{$redirectEsc}">
            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" autocomplete="username" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <div class="actions">
                <span class="muted">Use your admin credentials</span>
                <button type="submit">Sign in</button>
            </div>
        </form>
    </div>
</body>
</html>
HTML;

    echo $html;
});

// Admin login submit
$router->post('/admin/login', function () {
    $token = $_POST['csrf_token'] ?? null;
    if (!Csrf::verify($token)) {
        header('Location: /admin/login');
        exit;
    }

    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;
    $redirect = $_POST['redirect'] ?? '/admin/users';
    if (!is_string($redirect) || strpos($redirect, '/') !== 0) {
        $redirect = '/admin/users';
    }

    if ($username && $password && Auth::attempt($username, $password)) {
        header('Location: ' . $redirect);
        exit;
    }

    header('Location: /admin/login');
    exit;
});

// Admin logout
$router->get('/admin/logout', function () {
    Auth::logout();
    header('Location: /');
    exit;
});

// Admin users list
$router->get('/admin/users', function () {
    Middleware::requireAuthRedirect();

    $users = Auth::listUsers();
    $csrf = Csrf::getToken();
    include __DIR__ . '/../admin/users.php';
});

// New post form
$router->get('/admin/posts/new', function () {
    Middleware::requireAuthRedirect();
    $csrf = Csrf::getToken();
    include __DIR__ . '/../admin/new_post.php';
});

// Create post handler
$router->post('/admin/posts', function () {
    Middleware::requireAuthRedirect();
    Middleware::ensureCsrfPostOrRedirect();

    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';

    if ($title === '') {
        header('Location: /admin/posts/new');
        exit;
    }

    // Use the Blog Post model if available
    $postClass = 'Modules\\Blog\\Models\\Post';
    if (class_exists($postClass)) {
        // create a new id based on timestamp + random suffix and ensure uniqueness
        $id = (string) time();
        $posts = $postClass::all();

        $exists = function ($id, $posts) {
            foreach ($posts as $p) {
                if (isset($p['id']) && (string)$p['id'] === (string)$id) {
                    return true;
                }
            }
            return false;
        };

        while ($exists($id, $posts)) {
            $id = (string) (time() + random_int(1, 9999));
        }

        $newPost = [
            'id' => $id,
            'title' => $postClass::sanitizeContent($title),
            'content' => $postClass::sanitizeContent($content),
            'created_at' => date('c'),
        ];

        // append to posts array
        $posts[] = $newPost;

        // write back to file
        $dataFile = __DIR__ . '/../modules/blog/data/posts.json';
        $dir = dirname($dataFile);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($dataFile, json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    header('Location: /blog');
    exit;
});

// Add admin user
$router->post('/admin/users', function () {
    Middleware::requireAuthRedirect();
    Middleware::ensureCsrfPostOrRedirect();

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($username && $password) {
        Auth::addUser($username, $password);
    }

    header('Location: /admin/users');
});

// Delete admin user
$router->post('/admin/users/delete', function () {
    Middleware::requireAuthRedirect();
    Middleware::ensureCsrfPostOrRedirect();

    $username = $_POST['username'] ?? '';
    if ($username) {
        Auth::deleteUser($username);
    }

    header('Location: /admin/users');
});

// Initialize modules and let them register routes with the router
$modules = new ModuleManager(__DIR__ . '/../modules');
$modules->loadModules($router);

$router->dispatch($_SERVER['REQUEST_URI']);
