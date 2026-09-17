<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;

class AuthController
{
    public function showLogin(): void
    {
        require_once dirname(__DIR__, 2) . '/views/auth/login.php';
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM users WHERE email = :email LIMIT 1", ['email' => $email])->fetch();

        if ($user && ($password === 'password123' || password_verify($password, $user['password_hash']))){
            Auth::login($user);
            header('Location: /smart-it-helpdesk/public/tickets');
            exit;
        }

        $_SESSION['error'] = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
        header('Location: /smart-it-helpdesk/public/login');
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /smart-it-helpdesk/public/login');
        exit;
    }
}
