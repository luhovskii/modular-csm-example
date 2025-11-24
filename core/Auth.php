<?php
namespace Core;

class Auth
{
    public static function start()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function attempt(string $user, string $pass): bool
    {
        self::start();
        // ensure users file exists (seed from config.php if present)
        self::ensureUsersFile();

        $users = self::readUsers();
        if (isset($users[$user])) {
            $hash = $users[$user]['password'] ?? '';
            if (password_verify($pass, $hash)) {
                $_SESSION['is_admin'] = true;
                $_SESSION['user'] = $user;
                return true;
            }
        }

        return false;
    }

    public static function check(): bool
    {
        self::start();
        return ! empty($_SESSION['is_admin']);
    }

    public static function logout(): void
    {
        self::start();
        unset($_SESSION['is_admin']);
        unset($_SESSION['user']);
    }

    private static function usersFile(): string
    {
        return __DIR__ . '/../storage/users.json';
    }

    private static function ensureUsersFile(): void
    {
        $file = self::usersFile();
        if (! file_exists($file)) {
            $config = [];
            if (file_exists(__DIR__ . '/../config.php')) {
                $config = require __DIR__ . '/../config.php';
            }

            $user = $config['admin_user'] ?? null;
            $pass = $config['admin_pass'] ?? null;

            $data = [];
            if ($user && $pass) {
                $data[$user] = [ 'password' => password_hash($pass, PASSWORD_DEFAULT) ];
            }

            $dir = dirname($file);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    private static function readUsers(): array
    {
        $file = self::usersFile();
        if (! file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    public static function listUsers(): array
    {
        self::ensureUsersFile();
        return self::readUsers();
    }

    private static function writeUsers(array $data): bool
    {
        $file = self::usersFile();
        $dir = dirname($file);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }

    public static function addUser(string $username, string $password): bool
    {
        self::ensureUsersFile();
        $users = self::readUsers();
        if (isset($users[$username])) {
            return false; // already exists
        }
        $users[$username] = ['password' => password_hash($password, PASSWORD_DEFAULT)];
        return self::writeUsers($users);
    }

    public static function deleteUser(string $username): bool
    {
        self::ensureUsersFile();
        $users = self::readUsers();
        if (! isset($users[$username])) {
            return false;
        }
        unset($users[$username]);
        return self::writeUsers($users);
    }
}
