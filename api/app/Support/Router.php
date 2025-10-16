<?php
namespace App\Support;
use App\Http\Middleware\AuthMiddleware;

class Router {
    private array $routes = [];

    public function add(string $method, string $path, array $handler, bool $auth=false): void {
        // Convert /resource/:id to regex
        $pattern = preg_replace('#:([a-zA-Z_][a-zA-Z0-9_]*)#', '(?P<$1>[^/]+)', $path);
        $regex = '#^' . $pattern . '$#';
        $this->routes[] = ['method'=>$method, 'path'=>$path, 'regex'=>$regex, 'handler'=>$handler, 'auth'=>$auth];
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($base && str_starts_with($uri, $base)) $uri = substr($uri, strlen($base));
        $uri = '/' . ltrim($uri, '/');

        foreach ($this->routes as $r) {
            if ($r['method'] !== $method) continue;
            if (preg_match($r['regex'], $uri, $m)) {
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                if ($r['auth']) {
                    $user = AuthMiddleware::requireUser();
                    return call_user_func($r['handler'], $user, $params);
                }
                return call_user_func($r['handler'], $params);
            }
        }
        Response::json(['error'=>'Not found','path'=>$uri], 404);
    }
}
