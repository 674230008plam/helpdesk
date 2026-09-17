<?php
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/src/autoload.php';

if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $_ENV[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
        }
    }
}

ini_set('display_errors', '1');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['_csrf_token'])) {
    $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
}

use App\Core\Database;
use App\Core\Router;
use App\Core\Middleware;

try {
    Database::getInstance();
    $router = new Router();

    $router->get('/', function () {
        header('Location: /smart-it-helpdesk/public/tickets');
        exit;
    });

    $router->get('/login', 'App\Controllers\AuthController@showLogin');
    $router->post('/login', 'App\Controllers\AuthController@login');
    $router->post('/logout', 'App\Controllers\AuthController@logout');

    $router->get('/tickets', function() {
        Middleware::auth(function() { (new App\Controllers\TicketController())->index(); });
    });
    $router->get('/tickets/create', function() {
        Middleware::auth(function() { (new App\Controllers\TicketController())->create(); });
    });
    $router->post('/tickets', function() {
        Middleware::auth(function() { 
            Middleware::csrf(function() { (new App\Controllers\TicketController())->store(); });
        });
    });
    $router->get('/tickets/{id}', function($id) {
        Middleware::auth(function() use ($id) { (new App\Controllers\TicketController())->show($id); });
    });
    $router->post('/tickets/{id}/status', function($id) {
        Middleware::auth(function() use ($id) { 
            Middleware::csrf(function() use ($id) { (new App\Controllers\TicketController())->updateStatus($id); });
        });
    });
    $router->post('/tickets/{id}/comment', function($id) {
        Middleware::auth(function() use ($id) { 
            Middleware::csrf(function() use ($id) { (new App\Controllers\TicketController())->addComment($id); });
        });
    });
    $router->post('/tickets/{id}/rate', function($id) {
        Middleware::auth(function() use ($id) { 
            Middleware::csrf(function() use ($id) { (new App\Controllers\TicketController())->rate($id); });
        });
    });

    $router->get('/admin/dashboard', function() {
        Middleware::auth(function() {
            Middleware::role(['admin'])(function() {
                (new App\Controllers\DashboardController())->index();
            });
        });
    });

    $router->get('/storage/uploads/{file}', function ($file) {
        $filePath = BASE_PATH . '/storage/uploads/ticket-images/' . basename($file);
        if (file_exists($filePath)) {
            header('Content-Type: ' . (mime_content_type($filePath) ?: 'image/jpeg'));
            readfile($filePath);
            exit;
        }
        http_response_code(404);
        echo "404 Not Found";
    });

    $router->dispatch();

} catch (\Throwable $e) {
    http_response_code(500);
    echo "<div style='font-family: sans-serif; padding: 20px; background: #fee2e2; border-radius: 8px;'>";
    echo "<h2 style='color: #b91c1c;'>Application Error</h2>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " line " . $e->getLine() . "</p>";
    echo "<pre style='background: white; padding: 10px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}