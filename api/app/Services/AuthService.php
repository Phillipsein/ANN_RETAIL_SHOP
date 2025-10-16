<?php
namespace App\Services;
use App\Support\JWT;

class AuthService {
    public static function issueToken(array $user, array $roles, array $perms): string {
        $secret = getenv('APP_SECRET') ?: 'change_me';
        $now = time();
        $payload = [
            'sub'=>(int)$user['id'],
            'name'=>$user['name'],
            'email'=>$user['email'],
            'roles'=>$roles,
            'permissions'=>$perms,
            'iat'=>$now,
            'exp'=>$now + 3600
        ];
        return JWT::sign(['alg'=>'HS256','typ'=>'JWT'], $payload, $secret);
    }
}
