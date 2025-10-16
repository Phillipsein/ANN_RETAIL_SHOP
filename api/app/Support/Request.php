<?php
namespace App\Support;
class Request {
    public static function jsonBody(): array {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
    public static function param(array $params, string $name, $default=null) {
        return $params[$name] ?? $default;
    }
}
