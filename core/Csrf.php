<?php

namespace Core;

class Csrf
{
    public static function start()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function getToken(): string
    {
        self::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verify(?string $token): bool
    {
        self::start();
        $stored = $_SESSION['csrf_token'] ?? '';
        if (!is_string($token)) {
            return false;
        }
        return hash_equals($stored, $token);
    }
}
