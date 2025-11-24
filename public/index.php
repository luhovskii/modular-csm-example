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

    echo '<!doctype html><html><body>';
    echo '<h1>Admin Login</h1>';
    echo '<form method="POST" action="/admin/login">';
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') . '">';
    echo '<input type="hidden" name="redirect" value="' . htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') . '">';
    echo '<label>Username: <input name="username"></label><br/>';
    echo '<label>Password: <input type="password" name="password"></label><br/>';
    echo '<button>Login</button>';
    echo '</form>';
    echo '</body></html>';
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
