<?php

namespace Core;

class Middleware
{
    public static function requireAuthRedirect(): void
    {
        if (!Auth::check()) {
            header('Location: /admin/login');
            exit;
        }
    }

    public static function requireAuthJson(): void
    {
        if (!Auth::check()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
    }

    public static function ensureCsrfPostOrRedirect(): void
    {
        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::verify($token)) {
            header('Location: /admin/login');
            exit;
        }
    }

    public static function requireCsrfJsonOrFail(): void
    {
        $csrfToken = null;
        if (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'];
        } else {
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            if (!empty($headers['X-CSRF-Token'])) {
                $csrfToken = $headers['X-CSRF-Token'];
            } elseif (!empty($headers['x-csrf-token'])) {
                $csrfToken = $headers['x-csrf-token'];
            }
        }

        if (!Csrf::verify($csrfToken)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }
    }
}
