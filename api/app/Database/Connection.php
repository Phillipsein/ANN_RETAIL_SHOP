<?php
namespace App\Database;
use mysqli;
use App\Config\Config;

class Connection {
    private static ?mysqli $conn = null;

    public static function get(): mysqli {
        if (self::$conn === null) {
            $host = Config::env('DB_HOST','localhost');
            $db   = Config::env('DB_NAME','retail_app');
            $user = Config::env('DB_USER','root');
            $pass = Config::env('DB_PASS','');
            $conn = new mysqli($host, $user, $pass, $db);
            if ($conn->connect_error) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error'=>'DB connection failed']); exit;
            }
            $conn->set_charset('utf8mb4');
            self::$conn = $conn;
        }
        return self::$conn;
    }
}
