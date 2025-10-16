<?php
namespace App\Http\Middleware;
use App\Support\Response;
use App\Support\JWT;

class AuthMiddleware {
    public static function requireUser(): array {
        $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!str_starts_with($hdr, 'Bearer ')) Response::json(['error'=>'Missing Bearer token'], 401);
        $token = substr($hdr, 7);
        $secret = getenv('APP_SECRET') ?: 'change_me';
        if (!JWT::verify($token, $secret)) Response::json(['error'=>'Invalid token'], 401);
        $payload = JWT::decode($token);
        if (!$payload || ($payload['exp'] ?? 0) < time()) Response::json(['error'=>'Token expired'], 401);
        return $payload;
    }
}
