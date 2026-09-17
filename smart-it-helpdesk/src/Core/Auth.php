<?php
namespace App\Core;

class Auth
{
    public static function login(array $user): void
    {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function check(): bool { return isset($_SESSION['user']); }
    public static function user(): ?array { return $_SESSION['user'] ?? null; }
    public static function id(): ?int { return isset($_SESSION['user']) ? (int)$_SESSION['user']['id'] : null; }
    public static function role(): ?string { return $_SESSION['user']['role'] ?? null; }
}
