<?php
namespace App\Core;

class Middleware
{
    public static function auth(callable $next): void
    {
        if (!Auth::check()) {
            header('Location: /smart-it-helpdesk/public/login');
            exit;
        }
        $next();
    }

    public static function role(array $allowedRoles): \Closure
    {
        return function (callable $next) use ($allowedRoles) {
            $userRole = Auth::role();
            if (!in_array($userRole, $allowedRoles, true)) {
                http_response_code(403);
                echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
                echo "<h2>403 Forbidden: ไม่มีสิทธิ์เข้าถึงส่วนนี้</h2>";
                echo "<a href='/smart-it-helpdesk/public/tickets'>กลับหน้ารายการ</a>";
                echo "</div>";
                exit;
            }
            $next();
        };
    }

    public static function csrf(callable $next): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_csrf_token'] ?? null;
            if (!$token || !hash_equals($_SESSION['_csrf_token'] ?? '', $token)) {
                http_response_code(403);
                die("403 Forbidden: CSRF Token Validation Failed");
            }
        }
        $next();
    }
}
