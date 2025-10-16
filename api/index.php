<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap/app.php';

use App\Support\Router;

// Basic CORS
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$router = new Router();

require BASE_PATH . '/app/Http/Routes/api.php';

$router->dispatch();
