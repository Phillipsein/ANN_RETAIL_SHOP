<?php
namespace App\Support;
use App\Support\Response;
class Perms {
    public static function require(array $user, string $permission): void {
        $perms = $user['permissions'] ?? [];
        if (!in_array($permission, $perms, true)) {
            Response::json(['error'=>'Forbidden: ' . $permission], 403);
        }
    }
}
