<?php
namespace App\Config;
class Config {
    public static function env(string $key, ?string $default=null): ?string {
        $v = getenv($key);
        return $v === false ? $default : $v;
    }
}
