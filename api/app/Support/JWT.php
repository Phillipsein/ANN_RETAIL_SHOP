<?php

namespace App\Support;

class JWT
{
    public static function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    public static function base64url_decode(string $data): string|false
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
    public static function sign(array $header, array $payload, string $secret): string
    {
        $h = self::base64url_encode(json_encode($header));
        $p = self::base64url_encode(json_encode($payload));
        $s = self::base64url_encode(hash_hmac('sha256', $h . '.' . $p, $secret, true));
        return $h . '.' . $p . '.' . $s;
    }
    public static function verify(string $jwt, string $secret): bool
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return false;
        [$h, $p, $s] = $parts;
        $sig = self::base64url_encode(hash_hmac('sha256', $h . '.' . $p, $secret, true));
        return hash_equals($sig, $s);
    }
    public static function decode(string $jwt): ?array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;
        return json_decode(self::base64url_decode($parts[1]), true);
    }
}
